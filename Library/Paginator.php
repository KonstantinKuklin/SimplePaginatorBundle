<?php

namespace Graybit\Bundle\SimplePaginatorBundle\Library;
/**
 * Based on:
 * @author      Mardix
 * @github      http://github.com/mardix/Paginator
 *
 * Refactor:
 * @author: Konstantin Kuklin <konstantin.kuklin@gmail.com>
 * Date: 05.09.12
 */
class Paginator
{
    /**
     * Holds params
     * @var array
     */
    protected $params = array();

    /**
     * Holds the template url
     * @var string
     */
    protected $templateUrl = "";

    /**
     * Constructor
     *
     * @param int $currentPage
     * @param int $totalItems - Total items found
     * @param int $itemPerPage - Total items per page
     * @param int $navigationSize - The naviagation set size
     */
    public function __construct($currentPage = 1, $totalItems = 0, $itemPerPage = 10, $navigationSize = 10)
    {

        $this->setCurrentPage($currentPage);

        $this->setTotalItems($totalItems);

        $this->setItemsPerPage($itemPerPage);

        $this->setNavigationSize($navigationSize);

        $this->setPrevNextTitle();

    }

    /**
     * To set the previous and next title
     * @param string $prev
     * @param string $next
     * @param string $next
     * @return \Graybit\Bundle\SimplePaginatorBundle\Library\Paginator
     */
    public function setPrevNextTitle($prev = "Prev", $next = "Next")
    {
        $this->params["prevTitle"] = $prev;
        $this->params["nextTitle"] = $next;
        return $this;
    }

    /**
     * Set the total items. It will be used to determined the size of the pagination set
     * @param int $items
     * @return \Graybit\Bundle\SimplePaginatorBundle\Library\Paginator
     */
    public function setTotalItems($items = 0)
    {
        $this->params["totalItems"] = $items;
        return $this;
    }

    /**
     * Get the total items
     * @return int
     */
    public function getTotalItems()
    {
        return $this->params["totalItems"];
    }

    /**
     * Set the items per page
     * @param int $ipp
     * @return \Graybit\Bundle\SimplePaginatorBundle\Library\Paginator
     */
    public function setItemsPerPage($ipp = 10)
    {
        $this->params["itemsPerPage"] = $ipp;
        return $this;
    }

    /**
     * Retrieve the items per page
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->params["itemsPerPage"];
    }

    /**
     * Set the current page
     * @param int $page
     * @return \Graybit\Bundle\SimplePaginatorBundle\Library\Paginator
     */
    public function setCurrentPage($page = 1)
    {
        $this->params["currentPage"] = $page;
        return $this;
    }

    /**
     * Get the current page
     * @return type
     */
    public function getCurrentPage()
    {
        return
            ($this->params["currentPage"] <= $this->getTotalPages())
                ? $this->params["currentPage"]
                : $this->getTotalPages();
    }

    /**
     * Get the pagination start count
     * @return int
     */
    public function getStartCount()
    {
        return (int)($this->getItemsPerPage() * ($this->getCurrentPage() - 1));
    }

    /**
     * Get the pagination end count
     * @return int
     */
    public function getEndCount()
    {
        return (int)((($this->getItemsPerPage() - 1) * $this->getCurrentPage()) + $this->getCurrentPage());
    }

    /**
     * Return the offset for sql queries, specially
     * @return START,LIMIT
     *
     * @tip: SQL tip. It's best to do two queries one with SELECT COUNT(*) FROM tableName WHERE X
     *       set the setTotalItems()
     */
    public function getSQLOffset()
    {
        return $this->getStartCount() . "," . $this->getItemsPerPage();
    }

    /**
     * Get the total pages
     * @return int
     */
    public function getTotalPages()
    {
        return @ceil($this->getTotalItems() / $this->getItemsPerPage());
    }

    /**
     * Set the navigation size
     * @param int $set
     * @return Graybit\Bundle\SimplePaginatorBundle\Library\Paginator
     */
    public function setNavigationSize($set = 10)
    {
        $this->params["navSize"] = $set;
        return $this;
    }

    /**
     * Get the navigation size
     * @return int
     */
    public function getNavigationSize()
    {
        return
            $this->params["navSize"];
    }

    /*******************************************************************************/

    /**
     * toArray() export the pagination into an array. This array can be used for your own template or for other usafe
     * @return Array
     *     Array(
     *          array(
     *                "PageNumber", // the page number
     *                "Label", // the label for the page number
     *                "Url", // the url
     *                "isCurrent" // bool  set if page is current or not
     *          )
     *      )
     */
    public function toArray()
    {

        $Navigation = array();

        $totalPages = $this->getTotalPages();
        $navSize = $this->getNavigationSize();
        $currentPage = $this->getCurrentPage();

        if ($totalPages) {

            $halfSet = @ceil($navSize / 2);
            $start = 1;
            $end = ($totalPages < $navSize) ? $totalPages : $navSize;

            $usePrevNextNav = ($totalPages > $navSize) ? true : false;

            if ($currentPage >= $navSize) {
                $start = $currentPage - $navSize + $halfSet + 1;
                $end = $currentPage + $halfSet - 1;
            }

            if ($end > $totalPages) {
                $s = $totalPages - $navSize;
                $start = $s ? $s : 1;
                $end = $totalPages;
            }

            // Previous
            $prev = $currentPage - 1;
//            if ($currentPage >= $navSize && $usePrevNextNav) {
//                $Navigation[] = array(
//                    "PageNumber" => $prev,
//                    "Label" => $this->prevTitle,
//                    "Url" => $this->parseTplUrl($prev),
//                    "isCurrent" => false
//                );
//            }

            // All the pages
            for ($i = $start; $i <= $end; $i++) {
                $Navigation[] = array(
                    "PageNumber" => $i,
                    "Label" => $i,
                    "Url" => $this->parseTplUrl($i),
                    "isCurrent" => ($i == $currentPage) ? true : false,
                );
            }

            // Next
            $next = $currentPage + 1;

            $nav = array();
            if ($prev > 0 && $prev != $currentPage) {
                $nav['prev'] = array(
                    "PageNumber" => $prev,
                );
            }

            if ($end <= $totalPages && $end != $currentPage) {
                $nav['next'] = array(
                    "PageNumber" => $next,
                );
            }

        }
        return array(
            'prev' => (isset($nav['prev'])? $nav['prev'] : ''),
            'next' => (isset($nav['next'])? $nav['next'] : ''),
            'items' => $Navigation,
        );
    }


    /**
     * Render the paginator in HTML format
     * @param int $totalItems - The total Items
     * @param string $paginationClsName - The class name of the pagination
     * @param string $wrapTag
     * @param string $listTag
     * @return string
     * <div class='pagination'>
     *      <ul>
     *          <li>1</li>
     *          <li class='active'>2</li>
     *          <li>3</li>
     *      <ul>
     * </div>
     */
    public function render($totalItems = 0, $paginationClsName = "pagination", $wrapTag = "ul", $listTag = "li")
    {

        $this->listTag = $listTag;

        $this->wrapTag = $wrapTag;
        $pagination = '';

        foreach ($this->toArray($totalItems) as $page) {
            $pagination .= $this->wrapList($this->aHref($page["Url"], $page["Label"]), $page["isCurrent"], false);
        }

        return
            "<div class=\"{$paginationClsName}\">
                <{$this->wrapTag}>{$pagination}</{$this->wrapTag}>
            </div>";

    }

    /*******************************************************************************/

    /**
     * Parse a page number in the template url
     * @param int $pageNumber
     * @return string
     */
    protected function parseTplUrl($pageNumber)
    {
        return str_replace("(#pageNumber)", $pageNumber, $this->templateUrl);
    }

    /**
     * To create an <a href> link
     * @param $url
     * @param string $txt
     * @return string
     */
    protected function aHref($url, $txt)
    {
        return "<a href=\"{$url}\">{$txt}</a>";
    }

    /**
     * Create a wrap list, ie: <li></li>
     * @param string $html
     * @param bool $isActive - To set the active class in this element
     * @param bool $isDisabled - To set the disabled class in this element
     * @return string
     */
    protected function wrapList($html, $isActive = false, $isDisabled = false)
    {
        $activeCls = $isActive ? " active " : "";
        $disableCls = $isDisabled ? " disabled " : "";


        return "<{$this->listTag} class=\"{$activeCls} {$disableCls}\">{$html}</{$this->listTag}>\n";
    }


    /**
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->params[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->params[$key];
    }

    public function __toString()
    {
        return $this->render();
    }


}
