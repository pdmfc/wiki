<?php

namespace Pdmfc\Wiki\Traits;

use Symfony\Component\DomCrawler\Crawler;

trait Indexable
{
    /**
     * @param  $version
     * @param  $role
     * @return mixed
     */
    public function index($version, $role)
    {
        return $this->cache->remember(function () use ($version, $role) {
            $pages = $this->getPages($version, $role);

            $result = [];
            foreach($pages as $page) {
                $page = explode("{{link_menu}}", $page)[1];
                $pageContent = $this->get($version, $role, $page);

                if(! $pageContent)
                    continue;

                $indexableNodes = implode(',', config('wiki.search.engines.internal.index'));

                $nodes = (new Crawler($pageContent))
                    ->filter($indexableNodes)
                    ->each(function (Crawler $node, $i) {
                        $url = preg_match('/<a href="(.+)">/', $node->html(), $match);
                        $link = null;
                        if(isset($match[1])) {
                            $link = parse_url($match[1], PHP_URL_FRAGMENT);
                        }
                        return [
                            'link' => $link,
                            'text' => preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', (str_replace("-", " ", $node->text())))
                        ];
                    });

                $title = (new Crawler($pageContent))
                        ->filter('h1')
                        ->each(function (Crawler $node, $i) {
                            return $node->text();
                        });

                $result[] = [
                    'path'     => $page,
                    'title'    => $title ? $title[0] : '',
                    'headings' => $nodes
                ];
            }

            return $result;
        }, 'larecipe.docs.'.$version.'.search');
    }

    /**
     * @param  $version
     * @param  $role
     * @return mixed
     */
    protected function getPages($version, $role)
    {
        $path = base_path(config('wiki.docs.path').'/'.$version.'/menu.'.$role.'.md');

        // match all markdown urls => [title](url)
        preg_match_all('/\(([^)]+)\)/', $this->files->get($path), $matches);

        return $matches[1];
    }
}
