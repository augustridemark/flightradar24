<?php
//Note: This would probably be an abstract class that multiple api classes would extend if there were more functions 
class API {
	private array $data = [];
	private string $method;
	private string $endpoint;
	private array $request_body;
	private $ticket;

	public function __construct($method, $endpoint, $token, $request_body) {
		//Note: simple no-dependency solution for .env file
		$local_env	= parse_ini_file(rtrim(__DIR__,'/app').'/app.env');
		if(!isset($local_env['TOKEN']) || $local_env['TOKEN'] != $token) {
			$this->requestError('Invalid token', 401);
			exit();
		}
		//Note: no autoloader for simplicity
		include 'ticket.php';
		$this->method		= $method;
		$this->endpoint		= $endpoint;
		$this->request_body	= $request_body;
		$this->ticket		= new Ticket();
	}

	/**
	 * Validates request method and endpoint
	 */
	public function processRequest() {
		if(!method_exists($this, $this->endpoint)) {
			$this->requestError('Not a valid endpoint');
			exit();
		}
		switch($this->method) {
			case 'POST':
				$this->{$this->endpoint}($this->request_body);
				break;
			default:
				$this->requestError('Not a valid request method');
				break;
		}
	}

	/**
	 * @param string $message Error message to return
	 * @param int $code HTPP response code to return, default 404
	 */
	private function requestError(string $message, int $code=404) {
		http_response_code($code);
		$this->data = ['error' => $message];
	}

	/**
	 * @param array $request_body raw body data from request
	 */
	public function createTicket(array $request_body) {
		$response = $this->ticket->createTicket($request_body);
		if(!empty($response)) {
			http_response_code(200);
			$this->data = $response;
		} else {
			$this->requestError('Could not create ticket', 400);
		}
	}

	/**
	 * @param array $request_body raw body data from request
	 */
	public function cancelTicket(array $request_body) {
		if(isset($request_body['ticket_id'])) {
			$response = $this->ticket->cancelTicket($request_body['ticket_id']);
			http_response_code(200);
			$this->data = $response;
		} else {
			$this->requestError('Missing parameter ticket_id', 400);
		}
	}

	/**
	 * @param array $request_body raw body data from request
	 */
	public function changeSeat(array $request_body) {
		if(isset($request_body['ticket_id'])) {
			$response = $this->ticket->changeSeat($request_body['ticket_id']);
			http_response_code(200);
			$this->data = $response;
		} else {
			$this->requestError('Missing parameter ticket_id', 400);
		}
	}

	public function __destruct() {
		ob_clean();
		header('Content-Type: application/json');
		echo json_encode($this->data);
		exit();
	}
}