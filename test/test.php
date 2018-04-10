<?php

	require '../src/link_runner.php';


	$linkRunner = new LinkRunner;

	$linkRunner->target = 'https://devdocs.io';

	$linkRunner->limit = 50;

	$linkRunner->create_sitemap();

	$linkRunner->save_sitemap();