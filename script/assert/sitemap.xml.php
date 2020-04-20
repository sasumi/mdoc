<\?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <?php foreach($article_list as $sum):?>
    <url>
        <loc><?=str_replace('.md', '.html', $sum['loc']);?></loc>
        <title><?=$sum['title'];?></title>
        <lastmod><?=$sum['modify_time'];?></lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>
    <?php endforeach;?>
</urlset>