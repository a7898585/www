<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 *分页模板类
 *
 */

class CI_Page
{
     var  $total;        //记录总数
     var  $pagesize;     //每一页显示的记录数
     var  $pages;        //总页数
     var  $cur_page;     //当前页码
     var  $offset;       //记录偏移量
     var  $baseLink;    //页面的基本URL地址
	 var  $explodeStr;    //页面的基本URL地址


    function page($total,$cur_page = 1,$pagesize = 20,$baseLink = '',$explodeStr='')
    {
        $this->total         = $total;
		$this->explodeStr    = $explodeStr;
        $this->pagesize = $pagesize;
        $this->cur_page = $this->curPage($cur_page);
        $this->totalPage();

		if($this->cur_page>$this->pages)
		{
            $this->cur_page = $this->pages;
		}

		if($this->cur_page<1)
		{
            $this->cur_page = 1;
		}
        $this->offset();
        $this->setBaseLink($baseLink);
    }

    /**
     * 设置URL链接
     * $param: name = value
     */
    function setBaseLink($baseLink)
    {
        if(!isset($baseLink) || empty($baseLink))
        {
            //$this->baseLink =$PHP_SELF.",";
            //$this->baseLink =$PHP_SELF;
            //print_r($_GET);
            $i = 0;

            foreach($_GET as $k => $v)
            {
                $v = str_replace(' ', '+', $v);
                if($i==0)
                {
                    $i++;
                    $this->baseLink = "$k,";
                    continue;
                }

                if ($k != 'page' && isset($v))
                {
                    $this->baseLink .= "$k=$v&";
                }
            }
            foreach($_POST as $k => $v)
            {

                if ($k != 'page' && isset($v))
                {
                    $this->baseLink .= "$k=$v&";
                }
            }
        }
        else
        {
			if($this->explodeStr!='')
			{
               $this->baseLink = $baseLink.$this->explodeStr;
			}else
			{
				$pos = strrpos($baseLink,"?");
				if($pos === false)
				{
					//不存在
					$this->baseLink = $baseLink."?";
				}
				elseif($pos == (strlen($baseLink)-1))
				{
					//位置在最后
					$this->baseLink = $baseLink;
				}
				else
				{
					$pos = strrpos($baseLink,"&");
					if($pos == (strlen($baseLink)-1))
					{
						//位置在最后
						$this->baseLink = $baseLink;
					}
					else
					{
						//不存在& 或不是在最后
						$this->baseLink = $baseLink."&";
					}
				}
			}
        }
        //cho $this->baseLink;
    }

    function totalPage()
    {

        $this->pages = ceil($this->total / $this->pagesize);
        return $this->pages;

    }

    function curPage($cur_page) //设置页数
    {
        if($cur_page)
        {
            $this->cur_page=intval($cur_page);
        }
        else
        {
            $this->cur_page=1; //设置为第一页
        }
        return  $this->cur_page;
   }


    function offset()
    {
        $this->offset=$this->pagesize * ($this->cur_page - 1);
        return $this->offset;
    }

    /**
     * 当前要取的最大记录
     */
    function maxCount()
    {
        return ($this->offset + $this->pagesize);
    }

    function upDownList()
    {
      $pager_Links = "(每页显示{$this->pagesize}条,当前页数：".$this->cur_page."　/　".$this->pages.")　";
      if($this->cur_page == 1 && $this->pages >1)
      {
            //第一页
            $pager_Links .= "首　页　|　上一页　|　<a href=".$this->baseLink."page=".($this->cur_page+1).">下一页</a>　|　<a href=".$this->baseLink."page=$this->pages>尾　页</a>";
      }
      elseif($this->cur_page == $this->pages && $this->pages > 1)
      {
           //最后一页
           $pager_Links .= "<a href=".$this->baseLink."page=1>首　页<a>　|　<a href=".$this->baseLink."page=".($this->cur_page-1).">上一页</a>　|　下一页　|　尾　页";
      }
      elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages)
      {
          //中间
          $pager_Links .= "<a href=".$this->baseLink."page=1>首　页<a>　|　<a href=".$this->baseLink."page=".($this->cur_page-1).">上一页</a>　|　<a href=".$this->baseLink."page=".($this->cur_page+1).">下一页</a>　|　<a href=".$this->baseLink."page=$this->pages>尾　页</a>";
      }
      else
      {
         $pager_Links .= "首　页　|　上　页　|　下一页　|　尾　页";
      }
        return $pager_Links;
    }

    function upDownListMin()
    {
      $pager_Links = "";
      if($this->cur_page == 1 && $this->pages >1)
      {
            //第一页
            $pager_Links .= "首　页　|　上一页　|　<a href=".$this->baseLink."page=".($this->cur_page+1).">下一页</a>　|　<a href=".$this->baseLink."page=$this->pages>尾　页</a>";
      }
      elseif($this->cur_page == $this->pages && $this->pages > 1)
      {
           //最后一页
           $pager_Links .= "<a href=".$this->baseLink."page=1>首　页<a>　|　<a href=".$this->baseLink."page=".($this->cur_page-1).">上一页</a>　|　下一页　|　尾　页";
      }
      elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages)
      {
          //中间
          $pager_Links .= "<a href=".$this->baseLink."page=1>首　页<a>　|　<a href=".$this->baseLink."page=".($this->cur_page-1).">上一页</a>　|　<a href=".$this->baseLink."page=".($this->cur_page+1).">下一页</a>　|　<a href=".$this->baseLink."page=$this->pages>尾　页</a>";
      }
      else
      {
         $pager_Links .= "首　页　|　上　页　|　下一页　|　尾　页";
      }
      return $pager_Links;
    }


    function numLink($pernum = 5)
    {
        $setpage  = $this->cur_page ? ceil($this->cur_page / $pernum) : 1;
        $pagenum   = ($this->pages > $pernum) ? $pernum : $this->pages;
        if ($this->total  <= $this->pagesize)
        {
            $text  = '只有一页';
        }
        else
        {
            $text = '页数:'.$this->pages.'&nbsp;'.$this->pagesize.'个/页&nbsp;';
            if ($this->cur_page > 1)
            {
                $text .= "<a title=第一页 href='".$this->baseLink."page=1'>[1]</a>..";
            }
            if ($setpage > 1) {
                $lastsetid = ($setpage-1)*$pernum;
                $text .= "<a title=上一列 href='".$this->baseLink."page=$lastsetid'>[<<]</a>";
            }
            if ($this->cur_page > 1) {
                $pre = $this->cur_page-1;
                $text .= "<a title=上一页 href='".$this->baseLink."page=$pre'>[<]</a>";
            }
            $i = ($setpage-1)*$pernum;
            for($j=$i; $j<($i+$pagenum) && $j<$this->pages; $j++) {
                $newpage = $j+1;
                if ($this->_cur_page == $j+1) {
                    $text .= '<b>['.($j+1).']</b>';
                } else {
                    $text .= "<a href='".$this->baseLink."page=$newpage'>[".($j+1)."]</a>";
                }
            }
            if ($this->cur_page < $this->pages){
                $next = $this->cur_page+1;
                $text .= "<a title=下一页 href='".$this->baseLink."page=$next'>[>]</a>";
            }
            if ($setpage < $this->_total) {
                $nextpre = $setpage*($pernum+1);
                if($nextpre < $this->pages)
                $text .= "<a title=下一列 href='".$this->baseLink."page=$nextpre'>[>>]</a>";
            }
            if ($this->cur_page < $this->pages) {
                $text .= "..<a title=最后一页 href='".$this->baseLink."page=".$this->pages."'>[".$this->pages."]</a>";
            }
         }
            return $text;
    }

   function upDownListInput()
    {
      $pager_Links = "(每页显示{$this->pagesize}条,共".$this->total."条　当前页数：".$this->cur_page."　/　".$this->pages.")　";
      if($this->cur_page == 1 && $this->pages >1)
      {
            //第一页
            $pager_Links .= "首　页　|　上一页　|　<a href=".$this->baseLink."page=".($this->cur_page+1).">下一页</a>　|　<a href=".$this->baseLink."page=$this->pages>尾　页</a>";
      }
      elseif($this->cur_page == $this->pages && $this->pages > 1)
      {
           //最后一页
           $pager_Links .= "<a href=".$this->baseLink."page=1>首　页<a>　|　<a href=".$this->baseLink."page=".($this->cur_page-1).">上一页</a>　|　下一页　|　尾　页";
      }
      elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages)
      {
          //中间
          $pager_Links .= "<a href=".$this->baseLink."page=1>首　页<a>　|　<a href=".$this->baseLink."page=".($this->cur_page-1).">上一页</a>　|　<a href=".$this->baseLink."page=".($this->cur_page+1).">下一页</a>　|　<a href=".$this->baseLink."page=$this->pages>尾　页</a>";
      }
      else
      {
         $pager_Links .= "首　页　|　上　页　|　下一页　|　尾　页";
      }
	  $pager_Links .= '　跳转 <input type="text" size="3" name="" id="searchpage" onkeyup="value=value.replace(/[^\d]/g,\'\')"/> 页
  <input type="image" name="imageField" src="http://img.cnfol.com/newblog/images/tzsubmit.gif" align="absmiddle" onclick="javascript:SearchPage();"/><script>function SearchPage(){ pg="page="+document.getElementById(\'searchpage\').value;PageUrl=\''.$this->baseLink.'\'+pg; document.write(""); window.location.href=PageUrl; }</script>';
        return $pager_Links;
    }
}
?>
