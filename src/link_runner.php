<?php

/*
 * 
 *  
 * 
 */



class LinkRunner {

	public $target;
	public $list = [];
	public $limit = 100;

	public function get_site_content($url)
	{
		if(@function_exists('curl_init')) {  
			$ch = curl_init();  
			curl_setopt($ch, CURLOPT_URL, $url);  
			curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; CrawlBot/1.0.0)');  
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
			curl_setopt($ch, CURLOPT_HEADER, true);  
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);  
			curl_setopt($ch, CURLOPT_TIMEOUT, 5);  
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  
			curl_setopt($ch, CURLOPT_ENCODING, "");  
			curl_setopt($ch, CURLOPT_AUTOREFERER, true);  
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_MAXREDIRS, 15);                 
			$site = curl_exec($ch);  
			curl_close($ch);  
		} else {  
			// global $site;  
			$site = file_get_contents($url);  
		}  

		return $site;  
	}

	public function link_finder($content)
	{
		$htmlRegExp = "|<a.*(?=href=\"([^\"]*)\")[^>]*>([^<]*)</a>|i";
		preg_match_all($htmlRegExp, $content, $result);
		return $result;
	}

	public function create_sitemap()
	{
		$this->list = [$this->target];
		$targetHost = parse_url($this->target)['host'];

		for($i = 0; $i < count($this->list); $i++) {

			$linkContent = $this->get_site_content($this->list[$i]);
			$newList = $this->link_finder($linkContent)[1];
			for($j = 0; $j < count($newList); $j++) {

				if(count($this->list) === $this->limit) break;

				$link = $newList[$j];
				if (preg_match('#^https?://#i', $link) === 1) {

						if($targetHost === parse_url($link)["host"]) {
								if(!in_array($newList[$j], $this->list))
										$this->list[] = $newList[$j];
						}


				} else if(substr($newList[$j], 0, 1) == "/") {

					$newLink = $this->target . $newList[$j];
					if(!in_array($newLink, $this->list))
							$this->list[] = $newLink;
				}
			}
		}
	}

	public function save_sitemap()
	{
		if(file_exists('./sitemap.txt'))
			unlink('./sitemap.txt');	

		touch('./sitemap.txt');
		

		$sitemap = fopen('./sitemap.txt', 'w');
		for($i = 0; $i < count($this->list); $i++) {
			file_put_contents('./sitemap.txt', $this->list[$i].PHP_EOL , FILE_APPEND | LOCK_EX);
		}
		fclose($sitemap);
	}
}