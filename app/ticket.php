<?php
class Ticket {

	//Note: Simple no-dependency storage solution. Would be a separate class
	private function store(array $data) {
		file_put_contents("app_data.json", json_encode($data));
	}

	private function get() {
		if(is_file("app_data.json")) {
			return json_decode(file_get_contents("app_data.json"),true);
		}
		return [];
	}

	private function remove() {
		unlink("app_data.json");
	}

	private function getSeat():string{
		$columns	= ['A','B','C','D'];
		$column		= $columns[rand(0,3)];
		$row		= rand(1,32);
		$seat		= $column.$row;

		return $seat;
	}
	
	/**
	 * Note: Very basic data validation in this implementation. Would need to validate passport format and airport codes
	 * @param array $ticket_data Information to create a ticket
	 * @return array Returns ticket 
	 */
	public function createTicket(array $ticket_data):array {
		foreach(['passport_id','departure_airport','destination_airport'] as $key) {
			if(!isset($ticket_data[$key])) {
				return ['error' => 'Missing required ticket data for: '.$key];
			}
		}
		//ticket_id would presumably be generated in the database
		//departure_time only presented in human readable format
		//departure_time defaults to right now if it is not supplied
		//seat would need to be validated so it is not occupied
		$ticket = [
			'ticket_id'				=> bin2hex(random_bytes(16)),
			'passport_id'			=> $ticket_data['passport_id'],
			'departure_airport'		=> $ticket_data['departure_airport'],
			'destination_airport'	=> $ticket_data['destination_airport'],
			'departure_time'		=> $ticket_data['departure_time']??date("Y-m-d H:i"),
			'seat'					=> $this->getSeat()
		];

		$this->store($ticket);
		return $ticket;
	}

	/**
	 * Note: Would presumably include new seat as a parameter and validation if it is a valid seat and not occupied
	 * @param string $ticket_id Unique ticket ID to change seat for
	 * @return array Returns ticket on success, ticket_id and error on fail
	 */
	public function changeSeat(string $ticket_id):array {
		$ticket = $this->get();
		if(isset($ticket['ticket_id']) && $ticket['ticket_id'] == $ticket_id) {
			$ticket['seat'] = $this->getSeat();
			$this->store($ticket);
			return $ticket;
		}
		return ['ticket_id' => $ticket_id, 'error' => 'Ticket not found'];
	}

	/**
	 * @param string $ticket_id Unique ticket ID to cancel
	 * @return array Returns ticket_id and canceled:true on success, ticket_id, canceled:false and error on fail
	 */
	public function cancelTicket(string $ticket_id):array {
		$ticket = $this->get();
		if(isset($ticket['ticket_id']) && $ticket['ticket_id'] == $ticket_id) {
			$this->remove();
			return ['ticket_id' => $ticket_id, 'canceled' => true];
		}
		return ['ticket_id' => $ticket_id, 'canceled' => false, 'error' => 'Ticket not found'];
	}
}