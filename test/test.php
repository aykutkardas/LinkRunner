<?php

	require '../src/link_runner.php';


	$linkRunner = new LinkRunner;

	$linkRunner->target = 'https://devdocs.io';

	$linkRunner->limit = 5;

	$linkRunner->create_sitemap();

	$linkRunner->save_sitemap();