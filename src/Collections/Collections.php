<?php

namespace Damcclean\Systatic\Collections;

use Damcclean\Markdown\MetaParsedown;
use Damcclean\Systatic\Config\Config;
use Damcclean\Systatic\Compiler\Compiler;

class Collections
{
    public function __construct()
    {
        $this->config = new Config();
        $this->compiler = new Compiler();
        $this->parsedown = new MetaParsedown();
        $this->store = [];
    }

    /*
        Collect Markdown and HTML files to the store
    */

    public function collect()
    {
        $collections = [
            'pages' => [
                'name' => 'Pages',
                'permalink' => '/',
                'location' => './content/pages'
            ]
        ];

        if(array_key_exists('collections', $this->config->getArray())) {
            $collections = $this->config->get('collections');

            foreach($collections as $collection) {
                if(strpos($collection['location'], 'http') != false) {
                    //$this->remote($collection); WIP
                } else {
                    $markdown = [];
                    $html = [];

                    $markdown = array_merge(glob($collection['array'] . '/*.md', GLOB_BRACE), $markdown);
                    $markdown = array_merge(glob($collection['array'] . '/*/*.md', GLOB_BRACE), $markdown);
                    $markdown = array_merge(glob($collection['array'] . '/*.markdown', GLOB_BRACE), $markdown);
                    $markdown = array_merge(glob($collection['array'] . '/*/*.markdown', GLOB_BRACE), $markdown);

                    $html = array_merge(glob($collection['array'] . '/*.html', GLOB_BRACE), $html);
                    $html = array_merge(glob($collection['array'] . '/*/*.html', GLOB_BRACE), $html);

                    foreach($markdown as $file) {
                        $this->markdown($file, $collection);
                    }

                    foreach($html as $file) {
                        $this->html($file, $collection);
                    }
                }
            }
        }

        $this->save($this->store);

        foreach($this->store as $entry)
        {
            $this->compiler->compile($entry);
        }

        return true;
    }

    /*
        Save to store
    */

    public function save($store)
    {
        file_put_contents($this->config->get('locations.storage') . '/store.json', json_encode($store));
        return true;
    }

    /*  
        Fetch from store
    */

    public function fetch()
    {
        return json_decode(file_get_contents($this->config->get('locations.storage') . '/store.json'), true);
    }

    /*
        Fetch from store (As JSON)
    */

    public function fetchAsJson()
    {
        return file_get_contents($this->config->get('locations.storage') . '/store.json');
    }

    /*
        Get Markdown file information
    */

    public function markdown($file, $collection)
    {
        $filename = $file;
        $contents = file_get_contents($file);

        if(strpos($filename, '.md')) {
            $slug = basename($filename, '.md');
        } elseif(strpos($filename, '.markdown')) {
            $slug = basename($filename, '.markdown');
        }

        $title = $slug;
        $view = 'index';

        $markdown = $this->parsedown->text($contents);
        $frontMatter = $this->parsedown->meta($contents);

        if(array_key_exists('title', $frontMatter)) {
            $title = $frontMatter['title'];
        }

        if(array_key_exists('slug', $frontMatter)) {
            $slug = $frontMatter['slug'];

            if(!array_key_exists('title', $frontMatter)) {
                $title = $slug;
            }
        }

        if(array_key_exists('view', $frontMatter)) {
            if(file_exists($this->config->get('locations.views') . '/' . $frontMatter['view'] . '.blade.php')) {
                $view = $frontMatter['view'];
            } elseif(file_exists($this->config->get('locations.views') . '/' . str_replace('.', '/', $frontMatter['view']) . '.blade.php')) {
                $view = str_replace('.', '/', $frontMatter['view']);
            }
        } elseif(array_key_exists('view', $collection)) {
            if(file_exists($this->config->get('locations.views') . '/' . $collection['view'] . '.blade.php')) {
                $view = $collection['view'];
            }
        } elseif($slug !== 'index') {
            if(file_exists($this->config->get('locations.views') . '/' . $slug . '.blade.php')) {
                $view = $slug;
            }
        }

        if(endsWith($collection['permalink'], '/') != false) {
            $permalink = $collection['permalink'] . $slug . '.html';
        } else {
            $permalink = $collection['permalink'] . '/' . $slug . '.html';
        }

        $entry = [
            'filename' => $filename,
            'permalink' => $permalink,
            'title' => $title,
            'slug' => $slug,
            'view' => $view,
            'content' => $markdown,
            'meta' => $frontMatter
        ];

        array_push($this->store, $entry);

        return $entry;
    }

    /*
        Get HTML file information
    */

    public function html($file, $collection)
    {
        $filename = $file;
        $contents = file_get_contents($file);

        if(strpos($filename, '.html')) {
            $slug = basename($filename, '.html');
        }

        if(file_exists($this->config->get('locations.views') . '/' . $slug . '.blade.php')) {
            $view = $slug;
        }

        $title = $slug;
        $view = 'index';

        if(file_exists($this->config->get('locations.views') . '/' . $slug . '.blade.php')) {
            $view = $slug;
        }

        if(endsWith($collection['permalink'], '/') != false) {
            $permalink = $collection['permalink'] . $slug . '.html';
        } else {
            $permalink = $collection['permalink'] . '/' . $slug . '.html';
        }

        $entry = [
            'filename' => $filename,
            'permalink' => $permalink,
            'title' => $title,
            'slug' => $slug,
            'view' => $view,
            'content' => $contents,
            'meta' => []
        ];

        array_push($this->store, $entry);

        return $entry;
    }

    /*
        Remote collections
    */

    // public function remote($collection)
    // {
    //     $items = json_decode(file_get_contents($collection['remote']), true);
        
    //     foreach($items as $item) {
    //         $entry = [
    //             'filename' => $item['slug'],
    //             'title' => $item['title']['rendered'],
    //             'slug' => $item['slug'],
    //             'view' => 'index',
    //             'content' => $item['content']['rendered'],
    //             'meta' => [],
    //             'type' => 'remote'
    //         ];
    
    //         array_push($this->store, $entry);
    //         return $entry;
    //     }
    // }
}