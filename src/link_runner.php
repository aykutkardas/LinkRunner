<?php

/*
 * "LinkRunner"
 *  Creator Sitemap with PHP
 *  @author: Aykut Kardaş
 *  github: https://github.com/aykutkardas/LinkRunner
 */
	
    
   


class LinkRunner {

	public $target;
	private $list = [];
	public $limit = 100;

	private function list_push($link, $prefix = '')
	{
		if (!in_array($link, $this->list))
			$this->list[] = $prefix . $link;
	}

	public function get_list()
	{
		return $this->list;
	}

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
		$this->list = [rtrim($this->target, '/')];
		$targetHost = parse_url($this->target)['host'];

		for($i = 0; $i < count($this->list); $i++) {

			$linkContent = $this->get_site_content($this->list[$i]);
			$newList = $this->link_finder($linkContent)[1];

			for($j = 0; $j < count($newList); $j++) {

				if(count($this->list) === $this->limit) break;

				$link = rtrim($newList[$j], '/');

				if (preg_match('#^https?://#i', $link) === 1) {
					if($targetHost === parse_url($link)["host"])
							$this->list_push($link);
				} else if(mb_substr($link, 0, 1) == '/') {
					$this->list_push($link, $this->target);
				}
			}
		}
	}

	public function save_sitemap()
	{	
		$fileName = parse_url($this->target)['host'] . '_sitemap.txt';
		
		touch($fileName);
		
		$content = implode("\r\n", $this->list);

		$file = fopen($fileName , 'w');
		fwrite($file, $content);
		fclose($file);
	}
}
