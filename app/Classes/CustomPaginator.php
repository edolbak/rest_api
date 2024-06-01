<?php

namespace App\Classes;

class CustomPaginator extends \Illuminate\Pagination\Paginator
{
    protected bool $hasLinks=false;
    protected int $total_pages=0;
    protected int $total_users=0;
    protected string|null $nextPageUrl;
    protected string|null $previousPageUrl;
    public function __construct($items, $perPage, $currentPage, $options=[])
    {
        $this->currentPage = $currentPage;
        $this->perPage = $perPage;

        if(array_key_exists('links', $options) && $options['links']){
            $links = $options['links'];
            $this->setHasMore();
            $this->hasLinks = true;
            $this->nextPageUrl = array_key_exists('next_url', $links)?$links['next_url']:null;
            $this->previousPageUrl = array_key_exists('prev_url', $links)?$links['prev_url']:null;
            $this->total_pages = array_key_exists('total_pages', $options)?$options['total_pages']:0;
            $this->total_users = array_key_exists('total_users', $options)?$options['total_users']:0;

            if($this->currentPage<$this->total_pages){
                $this->setHasMore(true);
            }
        }else{
            parent::__construct();
        }


    }

    public function setHasMore(bool $has = false):void
    {
        $this->hasMore = $has;
    }

    public function nextPageUrl(){
        if($this->hasLinks){
            return $this->nextPageUrl;
        }else{
            parent::nextPageUrl();
        }
    }

    public function previousPageUrl()
    {
        if($this->hasLinks){
            return $this->previousPageUrl;
        }else{
            parent::previousPageUrl();
        }
    }

}
