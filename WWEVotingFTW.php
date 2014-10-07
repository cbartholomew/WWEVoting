<?php
define("GOOD_FILE_NAME", "url_good_output.txt");
define("BAD_FILE_NAME", "url_bad_output.txt");
// dean ambrose :26466854
// john cena:	:26466853
// poll 		:26708739
$url = "http://voting.wwe.com/vote.php?poll=26708739&option=26466854&_=";
$votingUrl = "http://www.wwe.com/wwe-voting/results/26708739/";

// randomize the voting request
function getRandom()
{
	$digits = 10;
	return  rand(pow(10, $digits-1), pow(10, $digits)-1);
}

while(true)
{
		// build request
		$options = array(
			HTTP => array(
				METHOD  => "GET"
				)			
		);

		$ident  = getRandom();
		$newUrl = $url . $ident;
		$newVotingUrl = $votingUrl . $ident;
		// create stream context
		$context = stream_context_create($options);
		$votingContext = stream_context_create($options);
		// read in stream
		$result = file_get_contents($newUrl, false, $context);
	
		printf("=======================================\n");
		printf("response: %s\n",$http_response_header[0]);
		printf("=======================================\n");
		// check if not found
		if($http_response_header[0] == "HTTP/1.0 404 Not Found")
		{
			file_put_contents(BAD_FILE_NAME, $newUrl . " " . $http_response_header[0] . "\n",FILE_APPEND);
			sleep(1);
			continue;
		}

		// set response
		$response = $result;
	
		// if the response is error - skip
		if($response == "// OK")
		{
			file_put_contents(GOOD_FILE_NAME,  $newUrl . "\n" ,FILE_APPEND);

			// read in stream
			$votingResponse = file_get_contents($newVotingUrl, false, $votingContext);
			// no result
			$votingResult = json_decode($votingResponse);
			printf("John Cena Votes: %d  %d%% \n",$votingResult->results[0]->votes,$votingResult->results[0]->percent);
			printf("Dean Ambrose Votes: %d %d%% \n",$votingResult->results[1]->votes,$votingResult->results[1]->percent);
			$deanResult = $votingResult->results[1]->votes / ($votingResult->results[0]->votes + $votingResult->results[1]->votes);
			$cenaResult = $votingResult->results[0]->votes / ($votingResult->results[0]->votes + $votingResult->results[1]->votes);
			$totalVotes = $votingResult->results[0]->votes + $votingResult->results[1]->votes;
			printf("Total Votes: %d \n", $totalVotes);
			printf("Dean's Real Percent %f4 \n",$deanResult);
			printf("Cena's Real Percent %f4 \n",$cenaResult);
		}
		else
		{
			printf("response NOT accepted \n");
		}
		printf("=======================================\n");
		sleep(3);
}
?>