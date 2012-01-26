# UpCloo Site Walker - [![Build Status](https://secure.travis-ci.org/wdalmut/upcloo-site-walker.png)](http://travis-ci.org/wdalmut/upcloo-site-walker?branch=master)

This software create the XML representation of a content page using UpCloo
meta information.

## How it works

```
php upcloo-walk.php --site http://walterdalmut.com --sitekey mysitekey 
```

Actually two walkers are provided, a spider crawler and the sitemap crawler.

By default the spider will be used but you can set the sitemap crawler using the ```--sitemap``` option.

```
php upcloo-walk.php --site http://walterdalmut.com/your-site-map.xml --sitemap --sitekey my-site-key
```

You can set the output folder using ```--output``` option.

```
php upcloo-walk.php --site http://walterdalmut.com --output /tmp/files/ --sitekey my-site-key
```

## Options
Options that you can use

```
Usage: upcloo-walk.php [ options ]
--site|--domain|-d|-s <string> Set the site to walk [mandatory]
--sitekey|-k <string>          Sitekey for the site [mandatory]
--output|-o <string>           Set the output directory [optional], if missing this folder will be set.
--sitemap|-m                   Use sitemap strategy during your walk...
```