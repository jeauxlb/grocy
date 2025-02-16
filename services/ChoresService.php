<?php

namespace Grocy\Services;

use \Grocy\Services\StockService;

class ChoresService extends BaseService
{
	const CHORE_PERIOD_TYPE_MANUALLY = 'manually';
	const CHORE_PERIOD_TYPE_DYNAMIC_REGULAR = 'dynamic-regular';
	const CHORE_PERIOD_TYPE_DAILY = 'daily';
	const CHORE_PERIOD_TYPE_WEEKLY = 'weekly';
	const CHORE_PERIOD_TYPE_MONTHLY = 'monthly';
	const CHORE_PERIOD_TYPE_YEARLY = 'yearly';

	const CHORE_ASSIGNMENT_TYPE_NO_ASSIGNMENT = 'no-assignment';
	const CHORE_ASSIGNMENT_TYPE_WHO_LEAST_DID_FIRST = 'who-least-did-first';
	const CHORE_ASSIGNMENT_TYPE_RANDOM = 'random';
	const CHORE_ASSIGNMENT_TYPE_IN_ALPHABETICAL_ORDER = 'in-alphabetical-order';

	public function __construct()
	{
		parent::__construct();
		$this->StockService = new StockService();
	}

	protected $StockService;

	public function GetCurrent()
	{
		$sql = 'SELECT * from chores_current';
		return $this->DatabaseService->ExecuteDbQuery($sql)->fetchAll(\PDO::FETCH_OBJ);
	}

	public function GetChoreDetails(int $choreId)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$usersService = new UsersService();
			$users = $usersService->GetUsersAsDto();

		$chore = $this->Database->chores($choreId);
		$choreTrackedCount = $this->Database->chores_log()->where('chore_id = ? AND undone = 0', $choreId)->count();
		$choreLastTrackedTime = $this->Database->chores_log()->where('chore_id = ? AND undone = 0', $choreId)->max('tracked_time');
		$nextExecutionTime = $this->Database->chores_current()->where('chore_id', $choreId)->min('next_estimated_execution_time');
		
		$lastChoreLogRow =  $this->Database->chores_log()->where('chore_id = ? AND tracked_time = ? AND undone = 0', $choreId, $choreLastTrackedTime)->fetch();
		$lastDoneByUser = null;
		if ($lastChoreLogRow !== null && !empty($lastChoreLogRow))
		{
			$lastDoneByUser = FindObjectInArrayByPropertyValue($users, 'id', $lastChoreLogRow->done_by_user_id);
		}

		$nextExecutionAssignedUser = null;
		if (!empty($chore->next_execution_assigned_to_user_id))
		{
			$nextExecutionAssignedUser = FindObjectInArrayByPropertyValue($users, 'id', $chore->next_execution_assigned_to_user_id);
		}

		return array(
			'chore' => $chore,
			'last_tracked' => $choreLastTrackedTime,
			'tracked_count' => $choreTrackedCount,
			'last_done_by' => $lastDoneByUser,
			'next_estimated_execution_time' => $nextExecutionTime,
			'next_execution_assigned_user' => $nextExecutionAssignedUser
		);
	}

	public function TrackChore(int $choreId, string $trackedTime, $doneBy = GROCY_USER_ID)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$userRow = $this->Database->users()->where('id = ?', $doneBy)->fetch();
		if ($userRow === null)
		{
			throw new \Exception('User does not exist');
		}

		$chore = $this->Database->chores($choreId);
		if ($chore->track_date_only == 1)
		{
			$trackedTime = substr($trackedTime, 0, 10) . ' 00??';
		}
		
		$logRow = $this->Database->chores_log()->createRow(array(
			'chore_id' => $choreId,
			'tracked_time' => $trackedTime,
			'done_by_user_id' => $doneBy
		));
		$logRow->save();
		$lastInsertId = $this->Database->lastInsertId();

		$this->CalculateNextExecutionAssignment($choreId);

		if ($chore->consume_product_on_execution == 1 && !empty($chore->product_id))
		{
			$this->StockService->ConsumeProduct($chore->product_id, $chore->product_amount, false, StockService::TRANSACTION_TYPE_CONSUME);
		}

		return $lastInsertId;
	}

	private function ChoreExists($choreId)
	{
		$choreRow = $this->Database->chores()->where('id = ?', $choreId)->fetch();
		return $choreRow !== null;
	}

	public function UndoChoreExecution($executionId)
	{
		$logRow = $this->Database->chores_log()->where('id = ? AND undone = 0', $executionId)->fetch();
		if ($logRow == null)
		{
			throw new \Exception('Execution does not exist or was already undone');
		}

		// Update log entry
		$logRow->update(array(
			'undone' => 1,
			'undone_timestamp' => date('Y-m-d H:i:s')
		));
	}

	public function CalculateNextExecutionAssignment($choreId)
	{
		if (!$this->ChoreExists($choreId))
		{
			throw new \Exception('Chore does not exist');
		}

		$chore = $this->Database->chores($choreId);
		$choreLastTrackedTime = $this->Database->chores_log()->where('chore_id = ? AND undone = 0', $choreId)->max('tracked_time');
		$lastChoreLogRow =  $this->Database->chores_log()->where('chore_id = ? AND tracked_time = ? AND undone = 0', $choreId, $choreLastTrackedTime)->fetch();
		$lastDoneByUserId = $lastChoreLogRow->done_by_user_id;
		
		$usersService = new UsersService();
		$users = $usersService->GetUsersAsDto();
		$assignedUsers = array();
		foreach ($users as $user)
		{
			if (in_array($user->id, explode(',', $chore->assignment_config)))
			{
				$assignedUsers[] = $user;
			}
		}

		$nextExecutionUserId = null;
		if ($chore->assignment_type == self::CHORE_ASSIGNMENT_TYPE_RANDOM)
		{
			// Random assignment and only 1 user in the group? Well, ok - will be hard to guess the next one...
			if (count($assignedUsers) == 1)
			{
				$nextExecutionUserId = array_shift($assignedUsers)->id;
			}
			else
			{
				// Randomness in small groups will likely often result in the same user, so try it as long as this is the case
				while ($nextExecutionUserId == null || $nextExecutionUserId == $lastDoneByUserId)
				{
					$nextExecutionUserId = $assignedUsers[array_rand($assignedUsers)]->id;
				}
			}
		}
		else if ($chore->assignment_type == self::CHORE_ASSIGNMENT_TYPE_IN_ALPHABETICAL_ORDER)
		{
			usort($assignedUsers, function($a, $b)
			{
				return strcmp($a->display_name, $b->display_name);
			});

			$nextRoundMatches = false;
			foreach ($assignedUsers as $user)
			{
				if ($nextRoundMatches)
				{
					$nextExecutionUserId = $user->id;
					break;
				}

				if ($user->id == $lastDoneByUserId)
				{
					$nextRoundMatches = true;
				}
			}

			// If nothing has matched, probably it was the last user in the sorted list -> the first one is the next one
			if ($nextExecutionUserId == null)
			{
				$nextExecutionUserId = array_shift($assignedUsers)->id;
			}
		}
		else if ($chore->assignment_type == self::CHORE_ASSIGNMENT_TYPE_WHO_LEAST_DID_FIRST)
		{
			$row = $this->Database->chores_execution_users_statistics()->where('chore_id = ?', $choreId)->orderBy('execution_count')->limit(1)->fetch();
			if ($row != null)
			{
				$nextExecutionUserId = $row->user_id;
			}
		}

		$chore->update(array(
			'next_execution_assigned_to_user_id' => $nextExecutionUserId
		));
	}
}
