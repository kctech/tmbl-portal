<?php
namespace App\Libraries;

use GuzzleHttp\Client;

class MABLead {

    public static $debug = true;
    public static $reporting = false;
    private $client_id = "71ac9f40-f78d-4b90-a639-2bd6232d56c9";
    private $client_secret = "ca20522e-c428-4f3e-a217-0a6c8c8cd396";
    private $token = "";

	public function __construct($debug = false)
	{
        static::$debug = $debug;
        $this->getToken();
    }

    private function getToken()
    {
        $headers = [
            'debug' => static::$debug,
            'headers' => [
                'cache-control' => 'no-cache',
                'Content-Type' => 'application/x-www-form-urlencoded'
            ], 'form_params' => [
                'grant_type'    => 'client_credentials',
                'client_id'     => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope'         => 'leads:write:import',
            ]
        ];

        $token_call = static::callApiEndpoint($headers, [], [], "https://sts.mymortgageaccount.co.uk/connect/token", 'POST', 'application/x-www-form-urlencoded', null, static::$debug);
        if (static::$debug) {
            dump($token_call);
        }
        if ($token_call->status) {
            $this->token = $token_call->data->access_token ?? null;
            if (static::$debug) {
                dump($this->token);
            }
            return true;
        }
    }

    private function makeHeaders($encoding = 'application/json')
    {
        $headers = [
            'debug' => static::$debug,
            'headers' => [
                'Content-Type' => $encoding,
                'Authorization' => 'Bearer '.$this->token,
            ]
        ];
        return $headers;
    }

    public function newLead($data, $endpoint = 'https://api.mymortgageaccount.co.uk/lead/leads/imports', $encoding = 'application/json')
    {
        /*
        full lead object
        {
            "mortgageBasis": 1,
            "prospectType": 1,
            "contactMethodTypeId": 3,
            "groupId": 0,
            "introducerId": "bf88ba72-2882-456c-a379-de17c9da330e",
            "introducerBranchId": "ab254dcf-24bf-4f78-82bf-d7e758d0d5fc",
            "introducerStaffId": "03580d2b-4aee-4983-b904-6016f142d9e6",
            "groupEmailAddress": "Devnoreply1@mab.org.uk",
            "submittedByName": "Create Local Lead Referer",
            "dateTimeGdprConsent": "2023-02-06T09:17:18.684Z",
            "mortgagePurpose": 1,
            "currentBuyingPosition": 2,
            "howCanWeHelp": 1,
            "plotNumber": 42,
            "foundFutureHome": false,
            "totalGrossSalary": 80000,
            "propertyValue": 300000,
            "deposit": 30000,
            "distributionType": 1,
            "distributionGroupId": null,
            "leadReferralType": 0,
            "timeOfReferral": "2023-02-06T09:17:18.684Z",
            "creationDate": "2023-02-06T09:17:18.684Z",
            "notes": "This is a note",
            "consenter": 0,
            "customers": [
                {
                    "id": "feecfecc-8cad-4175-a781-37ea4cb1e8b2",
                    "title": 1,
                    "firstName": "John",
                    "lastName": "Doe",
                    "emailAddress": "Devnoreply1@mab.org.uk",
                    "telephoneNumber": "07777777777",
                    "dateOfBirth": "2003-04-13T00:00:00Z",
                    "gender": 1,
                    "maritalStatus": 2,
                    "index": 0,
                    "employmentStatus": 1,
                    "workedLongerThan6MonthsForCurrentEmployer": true,
                    "retirementAge": 65,
                    "hasActiveUserAccount": false,
                    "midasProClientId": null
                }
            ],
            "allocatedFirmId": null,
            "allocatedFirmBranchId": null,
            "allocatedAdviserId": null,
            "shouldSendCustomerInviteEmail": true,
            "midasProClientFolderID": null,
            "customFields": {
            "additionalProp1": "string",
            "additionalProp2": "string",
            "additionalProp3": "string"
        }
        */

        $headers = $this->makeHeaders();
        $response = static::callApiEndpoint($headers, $data, [], $endpoint, 'POST', $encoding, null, static::$debug);
        return $response;
    }

    public function apiCall($data, $endpoint = 'https://api.mymortgageaccount.co.uk/lead/leads/imports', $encoding = 'application/json'){
        $headers = $this->makeHeaders();
        $response = static::callApiEndpoint($headers, $data, [], $endpoint, 'POST', $encoding, null, static::$debug);
    }

	private static function callApiEndpoint($headers, $body, $querystring, $endpoint, $method, $encoding = 'application/json', $additional_data = null, $debug = false)
	{
        //set standard output variables
		$output = new \stdClass();
		$output->status = false;

		if(str_contains($endpoint,"//")){
			$url_parts = parse_url($endpoint);
			$root_url = $url_parts['scheme']."://".$url_parts['host'];
			$endpoint = $url_parts['path'];
			if(!empty($url_parts["query"])){
				$endpoint .= "?". $url_parts["query"];
			}
		}else{
            return $output;
        }

		$output->endpoint = $root_url . $endpoint . qs(($querystring ?? []), true, []);
		$output->request = static::encodeIfRequired($body);
		$output->additional_data = $additional_data;
		$output->data = null;

		//die($output->endpoint."\n");

		/* GUZZLE */
		$client = new Client(['base_uri' => $root_url]);
		try {
			$request = $headers;
			if ($encoding == 'multipart/form-data') {
				$request['multipart'] = $body;
			} elseif ($encoding == 'application/x-www-form-urlencoded') {
				if (!empty($body)) {
					$request['form_params'] = $body;
				}
			} else {
				if(!empty($body)){
					$request['body'] = static::encodeIfRequired($body);
				}
			}
			if (!empty($querystring)) {
				$request['query'] = $querystring;
			}

			$output->request = $request;
			$response = $client->request($method, $endpoint, $request);
			switch ($response->getStatusCode()) {
				case 200;
				case 201;
				case 202;
					$output->status = true;
					break;
				case 400;
				case 401;
				case 404;
					$output->status = false;
					break;
			}
			//success
			$output->response_code = $response->getStatusCode();
			$response_body = $response->getBody()->getContents() ?? $response->getBody();
			if(is_json($response_body)){
				$output->data = json_decode($response_body);
			}else{
				$output->data = $response_body;
			}
		} catch (\GuzzleHttp\Exception\ClientException $response) {
			//likely auth or validation error
			static::reportStatus("ClientException");
			$output->data = static::interpretClientException($response);
			$output->response_code = $response->getCode();
		} catch (\GuzzleHttp\Exception\RequestException $response) {
			static::reportStatus("RequestException");
			$output->data = static::interpretRequestException($response);
			$output->response_code = $response->getCode();
			//$output->response_request = $response->getRequest();
		} catch (\Exception $response) {
			static::reportStatus("Exception");
			//unknown error
			$output->data = $response;
			$output->response_code = $response->getCode();
		}

		if ($debug) {
			dump($output);
		}
		return static::postApiCallInterpret($output);
	}

    public static function reportStatus($message){
		if(static::$reporting){
			echo $message . "\n";
		}
	}

    public static function postApiCallInterpret($output)
	{
		return $output;
	}

	public static function encodeIfRequired($data){
		if (!is_string($data)) {
			$data = json_encode($data);
		}
		return $data;
	}

	public static function interpretClientException($response)
	{
		return $response->getResponse()->getBody()->getContents() ?? $response->getMessage() ?? json_decode($response);
	}

	public static function interpretRequestException($response)
	{
		return $response->getResponse()->getBody()->getContents() ?? $response->getMessage() ?? json_decode($response);
	}

	public static function interpretException($response)
	{
		return $response;
	}
}
