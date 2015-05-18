<?php

use App\Bill;
use App\BillVote;
use Carbon\Carbon;
use App\Congressman;
use App\PlenarySession;
use Illuminate\Database\Seeder;
use App\PlenarySessionPresence;

class BillsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$congressmen = Congressman::all();

		DB::table('bills')->delete();
		DB::table('plenary_sessions')->delete();

		$plenary = PlenarySession::create(['date' => Carbon::now()]);

		$bill = Bill::create(['congressman_id' => 71, 'name' => 'Aumento de salario para os funcionarios em 2015']);

		foreach($congressmen as $congressman)
		{
			// 1 = Absent
			// 2 = Yes
			// 3 = No
			// 4 = Restrained
			// 5 = Didn't vote

			$vote = rand(1,5);

			if ($vote !== 1)
			{
				$presence = PlenarySessionPresence::create([
					'plenary_session_id' => $plenary->id,
				    'congressman_id' => $congressman->id,
				    'party_id' => $congressman->party_id,
				]);

				if ($vote !== 5)
				{
					BillVote::create([
						'bill_id' => $bill->id,
						'congressman_id' => $congressman->id,
						'plenary_session_id' => $plenary->id,
						'vote' => $vote == 2 ? 'yes' : ($vote == 3 ? 'no' : 'refrained')
					]);
				}
			}
		}
	}

}
