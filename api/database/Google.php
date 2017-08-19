<?php
namespace database;

class Google {
	private $yt;

	public function __construct($devkey) {
		$c = new \Google_Client();
		$c->setDeveloperKey($devkey);
		$this->yt = new \Google_Service_YouTube($c);
	}

	public function getIdentifier($query) {
		$res = $this->yt->search->listSearch('id,snippet', array(
			'q' => $query,
			'maxResults' => 5
		));
		foreach ($res['items'] as $searchResult) {
			switch ($searchResult['id']['kind']) {
				case 'youtube#video':
					return $searchResult['id']['videoId'];
			}
		}
	}
}
?>