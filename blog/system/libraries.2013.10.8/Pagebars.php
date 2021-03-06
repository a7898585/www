<?php 
/*
 * ==========================
 * Author : modify by Rlby
 * Date   : 200$this->pageBarSize-10-11
 * Role   : 分页类
 * ==========================
 */

class CI_Pagebars
{
     var  $total;        //记录总数
     var  $pagesize;     //每一页显示的记录数
     var  $pages;        //总页数
     var  $cur_page;     //当前页码
     var  $offset;       //记录偏移量
     var  $baseLink;     //页面的基本URL地址
     var  $nsplit;       //页码分隔符的类型
	 var  $pageBarSize = 5;    //页码数
       
    
    function Page($total,$cur_page = 1,$pagesize = 20,$baseLink = '',$nsplit = '/')
    {   
        $this->total    = $total;
        $this->pagesize = $pagesize;
        $this->cur_page = $this->curPage($cur_page);
        $this->nsplit   = $nsplit;
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
        $this->baseLink = $baseLink;
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
		if($this->pages < 2)return '';

		$pager_Links = "(每页{$this->pagesize}条,当前页: ".$this->cur_page."/".$this->pages.") ";

		$splitpages = $this->splitpage("bloglist");
		if($this->cur_page > $this->pageBarSize)
		{
			$page_bar_content = '&nbsp;&nbsp;<a href="'.$this->baseLink.$this->nsplit.($this->cur_page-$this->pageBarSize).'"><img src="http://img.cnfol.com/newblog/images/pagebar/4.gif" border="0"  title="上一组"></a>&nbsp;&nbsp;';
		}
		else
		{
			$page_bar_content = '&nbsp;&nbsp;<img src="http://img.cnfol.com/newblog/images/pagebar/4.gif" border="0" title="上一组">&nbsp;&nbsp;';
		}
		if($this->pages - $this->cur_page > $this->pageBarSize)
		{
			$page_bar_content1 = '&nbsp;&nbsp;<a href="'.$this->baseLink.$this->nsplit.($this->cur_page+$this->pageBarSize).'"><img src="http://img.cnfol.com/newblog/images/pagebar/3.gif" border="0" title="下一组"></a>&nbsp;&nbsp;';
		}
		else
		{
			$page_bar_content1 = '&nbsp;&nbsp;<img src="http://img.cnfol.com/newblog/images/pagebar/3.gif" border="0" title="下一组">&nbsp;&nbsp;';

		}
		if($this->cur_page == 1 && $this->pages >1) 
		{
			//第一页
			$pager_Links .= "<img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'>".$page_bar_content."<img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'>".$splitpages."<a href=".$this->baseLink.$this->nsplit.($this->cur_page+1)."><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'></a>".$page_bar_content1."<a href=".$this->baseLink.$this->nsplit."$this->pages><img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'></a>";
		} 
		elseif($this->cur_page == $this->pages && $this->pages > 1) 
		{
		   //最后一页
		   $pager_Links .= "<a href=".$this->baseLink.$this->nsplit."1><img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'></a>".$page_bar_content."<a href=".$this->baseLink.$this->nsplit.($this->cur_page-1)."><img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'></a>".$splitpages."<img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'>".$page_bar_content1."<img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'>";
		} 
		elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages) 
		{
			//中间
			$pager_Links .= "<a href=".$this->baseLink.$this->nsplit."1><img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'></a>".$page_bar_content."<a href=".$this->baseLink.$this->nsplit.($this->cur_page-1)." ><img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'></a>".$splitpages."<a href=".$this->baseLink.$this->nsplit.($this->cur_page+1)."><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'></a>".$page_bar_content1."<a href=".$this->baseLink.$this->nsplit."$this->pages><img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'></a>";
		} 
		else
		{
			$pager_Links .= "<img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0'>".$page_bar_content."<img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0'><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0'>".$page_bar_content1."<img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0'>";
		}
        return $pager_Links;
	}

	function BlogList()
	{
		if($this->pages < 2)return '';
		$pager_Links = "(每页{$this->pagesize}条,当前页: ".$this->cur_page."/".$this->pages.") ";
		if($this->cur_page == 1 && $this->pages >1) 
		{
			//第一页
			$pager_Links .= "首页 | 上一页 | <a href=".$this->baseLink.$this->nsplit.($this->cur_page+1).">下一页</a> | <a href=".$this->baseLink.$this->nsplit."$this->pages>尾页</a>";
		} 
		elseif($this->cur_page == $this->pages && $this->pages > 1) 
		{
			//最后一页
			$pager_Links .= "<a href=".$this->baseLink.$this->nsplit."1>首页</a> | <a href=".$this->baseLink.$this->nsplit.($this->cur_page-1).">上一页</a> | 下一页 | 尾页";
		} 
		elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages) 
		{
			//中间
			$pager_Links .= "<a href=".$this->baseLink.$this->nsplit."1>首页</a> | <a href=".$this->baseLink.$this->nsplit.($this->cur_page-1).">上一页</a> | <a href=".$this->baseLink.$this->nsplit.($this->cur_page+1).">下一页</a> | <a href=".$this->baseLink.$this->nsplit."$this->pages>尾页</a>";
		} 
		else
		{
			$pager_Links .= "首页 | 上一页 | 下一页 | 尾页";
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
			if ($setpage > 1)
			{
				$lastsetid = ($setpage-1)*$pernum;
				$text .= "<a title=上一列 href='".$this->baseLink."page=$lastsetid'>[<<]</a>";
			}
			if ($this->cur_page > 1)
			{
				$pre = $this->cur_page-1;
				$text .= "<a title=上一页 href='".$this->baseLink."page=$pre'>[<]</a>";
			}
			$i = ($setpage-1)*$pernum;
			for($j=$i; $j<($i+$pagenum) && $j<$this->pages; $j++) 
			{
				$newpage = $j+1;
				if ($this->_cur_page == $j+1)
				{
					$text .= '<b>['.($j+1).']</b>';
				}
				else
				{
					$text .= "<a href='".$this->baseLink."page=$newpage'>[".($j+1)."]</a>";
				}
			}            
			if ($this->cur_page < $this->pages)
			{
				$next = $this->cur_page+1;
				$text .= "<a title=下一页 href='".$this->baseLink."page=$next'>[>]</a>";
			}
			if ($setpage < $this->_total) 
			{
				$nextpre = $setpage*($pernum+1);
				if($nextpre < $this->pages)
				$text .= "<a title=下一列 href='".$this->baseLink."page=$nextpre'>[>>]</a>";
			}
			if ($this->cur_page < $this->pages)
			{
				$text .= "..<a title=最后一页 href='".$this->baseLink."page=".$this->pages."'>[".$this->pages."]</a>";
			}
		}
		return $text;
	}

	function upDownListAjax($id, $callback)
	{
		if($this->pages < 2)return '';
		$pager_Links = "(当前页数：".$this->cur_page."　/　".$this->pages.")　";
		$splitpages = $this->splitpage("tajax",$id, $callback);
		if($this->cur_page > $this->pageBarSize)
		{
			$page_bar_content = "&nbsp;&nbsp;<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".($this->cur_page-$this->pageBarSize)."')><img src='http://img.cnfol.com/newblog/images/pagebar/4.gif' border='0'  title='上一组'></a>&nbsp;&nbsp;";
		}
		else
		{
			$page_bar_content = '&nbsp;&nbsp;<img src="http://img.cnfol.com/newblog/images/pagebar/4.gif" border="0" title="上一组">&nbsp;&nbsp;';
		}
		if($this->pages - $this->cur_page > $this->pageBarSize)
		{
			$page_bar_content1 = "&nbsp;&nbsp;<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".($this->cur_page+$this->pageBarSize)."')><img src='http://img.cnfol.com/newblog/images/pagebar/3.gif' border='0' title='下一组'></a>&nbsp;&nbsp;";
		}
		else
		{
			$page_bar_content1 = '&nbsp;&nbsp;<img src="http://img.cnfol.com/newblog/images/pagebar/3.gif" border="0" title="下一组">&nbsp;&nbsp;';
		}
		if($this->cur_page == 1 && $this->pages >1) 
		{
            //第一页
            $pager_Links .= "<img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'>".$page_bar_content."<img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'>".$splitpages."<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".($this->cur_page+1)."')><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'></a>".$page_bar_content1."<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".$this->pages."')><img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'></a>";
		} 
		elseif($this->cur_page == $this->pages && $this->pages > 1) 
		{
			$pager_Links .= "<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','1')><img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'></a>".$page_bar_content."<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".($this->cur_page-1)."')><img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'></a>".$splitpages."<img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'>".$page_bar_content1."<img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'>";
		} 
		elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages) 
		{
			//中间
			$pager_Links .= "<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','1')><img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'></a>".$page_bar_content."<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".($this->cur_page-1)."')><img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'></a>".$splitpages."<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".($this->cur_page+1)."')><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'></a>".$page_bar_content1."<a href='javascript:void(0)' onclick=javascript:".$callback."('".$id."','".$this->pages."')><img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'></a>";
		} 
		else
		{
			$pager_Links .= "<img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0'>".$page_bar_content."<img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0'><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0'>".$page_bar_content1."<img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0'>";
		}
		return $pager_Links;
	}


	function AjaxUpdateList($div)
	{
		if($this->pages < 2)return '';
     
		$pager_Links = "(每页显示{$this->pagesize}条,当前页数：".$this->cur_page."　/　".$this->pages.")　";
		$splitpages = $this->splitpage("ajax",$div);
		if($this->cur_page > $this->pageBarSize)
		{
			$page_bar_content = "&nbsp;&nbsp;<a href='#".($this->cur_page-$this->pageBarSize)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.($this->cur_page-$this->pageBarSize)."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/4.gif' border='0'  title='上一组'></a>&nbsp;&nbsp;";
		}
		else
		{
			$page_bar_content = '&nbsp;&nbsp;<img src="http://img.cnfol.com/newblog/images/pagebar/4.gif" border="0" title="上一组">&nbsp;&nbsp;';
		}
		if($this->pages - $this->cur_page > $this->pageBarSize)
		{
			$page_bar_content1 = "&nbsp;&nbsp;<a href='#".($this->cur_page+$this->pageBarSize)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.($this->cur_page+$this->pageBarSize)."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/3.gif' border='0' title='下一组'></a>&nbsp;&nbsp;";
		}
		else
		{
			$page_bar_content1 = '&nbsp;&nbsp;<img src="http://img.cnfol.com/newblog/images/pagebar/3.gif" border="0" title="下一组">&nbsp;&nbsp;';
		}
		if($this->cur_page == 1 && $this->pages >1) 
		{
            //第一页
            $pager_Links .= "<img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'>".$page_bar_content."<img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'>".$splitpages."<a href='#".($this->cur_page+1)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.($this->cur_page+1)."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'></a>".$page_bar_content1."<a href='#".($this->pages)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.$this->pages."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'></a>";
		} 
		elseif($this->cur_page == $this->pages && $this->pages > 1) 
		{
           $pager_Links .= "<a href='#1' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit."1','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'></a>".$page_bar_content."<a href='#".($this->cur_page-1)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.($this->cur_page-1)."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'></a>".$splitpages."<img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'>".$page_bar_content1."<img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'>";
		} 
		elseif ($this->cur_page > 1 && $this->cur_page <= $this->pages) 
		{
			//中间
			$pager_Links .= "<a href='#1' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit."1','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0' title='首页'></a>".$page_bar_content."<a href='#".($this->cur_page-1)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.($this->cur_page-1)."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0' title='上一页'></a>".$splitpages."<a href='#".($this->cur_page+1)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.($this->cur_page+1)."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0' title='下一页'></a>".$page_bar_content1."<a href='#".($this->pages)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.$this->pages."','".$div."')><img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0' title='末页'></a>";
		} 
		else
		{
			$pager_Links .= "<img src='http://img.cnfol.com/newblog/images/pagebar/5.gif' border='0'>".$page_bar_content."<img src='http://img.cnfol.com/newblog/images/pagebar/1.gif' border='0'><img src='http://img.cnfol.com/newblog/images/pagebar/2.gif' border='0'>".$page_bar_content1."<img src='http://img.cnfol.com/newblog/images/pagebar/6.gif' border='0'>";
		}
		return $pager_Links;
	}
	function splitpage($name,$div='',$callback='')
	{
		$work['page'] = $this->cur_page - ($this->pageBarSize-1)/2;
		if ($work['page'] < 1) $work['page'] = 1;
		$work['end'] = $this->cur_page + ($this->pageBarSize-1)/2;
		if ( $work['end'] != $work['page'] + $this->pageBarSize ) $work['end'] = $work['page'] + ($this->pageBarSize-1);
		if ($work['end'] > $this->pages) $work['end'] = $this->pages;
		if ( $work['page'] != $work['end'] - $this->pageBarSize ) $work['page'] = $work['end'] - ($this->pageBarSize-1);
		if ($work['page'] < 1) $work['page'] = 1;

		$work['page_span'] = '&nbsp;&nbsp;<span class=current>';
		for( $i = $work['page']; $i <= $work['end']; $i++ ) {
			$PageNo = $i;

			if ($PageNo == $this->cur_page) {
                $work['page_span'] .= "<font color ='red'>&nbsp;{$PageNo}&nbsp;</font>";
            } else {
				if ($name == 'bloglist') {
					$work['page_span'] .= "&nbsp;<a href=".$this->baseLink.$this->nsplit.$PageNo.">{$PageNo}</a>&nbsp;";
				} else if ($name =='ajax') {
					$work['page_span'] .= "&nbsp;<a href='#".($PageNo)."' onclick=javascript:FunctionAjaxUpdate('".$this->baseLink.$this->nsplit.$PageNo."','".$div."')>{$PageNo}</a>&nbsp;";
				} else if ($name =='tajax') {
					$work['page_span'] .= "&nbsp;<a href='javascript:void(0)' onclick=javascript:".$callback."('".$div."','".$PageNo."')>{$PageNo}</a>&nbsp;";
				}
            }
		}
		$work['page_span'] .= '</span>&nbsp;&nbsp;';
		return $work['page_span'];
	}
}  
?>