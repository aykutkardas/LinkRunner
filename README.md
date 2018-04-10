# LinkRunner
Creator Sitemap with PHP


### Usage 

```php

$linkRunner = new LinkRunner;

// Target website
$linkRunner->target = 'https://devdocs.io';

// Link limit
$linkRunner->limit = 50;

// Add Links 
$linkRunner->create_sitemap();

// Create sitemap.txt
$linkRunner->save_sitemap(); 
    
```
