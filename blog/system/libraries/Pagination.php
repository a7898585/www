<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 4.3.2 or newer
 *
 * @package     CodeIgniter
 * @author      ExpressionEngine Dev Team
 * @copyright   Copyright (c) 2006, EllisLab, Inc.
 * @license     http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since       Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Pagination Class
 *
 * @package     CodeIgniter
 * @subpackage  Libraries
 * @category    Pagination
 * @author      ExpressionEngine Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/pagination.html
 */
class CI_Pagination {

    var  $total;        //记录总数
    var  $pagesize;     //每一页显示的记录数
    var  $pages;        //总页数
    var  $cur_page;     //当前页码
    var  $offset;       //记录偏移量
    var  $baseLink;     //页面的基本URL地址
    var  $nsplit;       //页码分隔符的类型
    var  $typestr = '';
    var  $nextpage;
    var  $parpage;     

    function page($total,$cur_page = 1,$pagesize = 20,$baseLink = '',$nsplit = '/')
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
        $this->otherPage();
    }

    /**
     * 设置URL链接
     * $param: name = value
     */
    function setBaseLink($baseLink)
    {
        $this->baseLink = $baseLink;
    }

    function settypestr($str)
    {
        $this->typestr = $str;
    }

    function totalPage() 
    { 
         
        $this->pages = ceil($this->total / $this->pagesize);
        return $this->pages;

    }

    function otherPage() 
    { 
        if(($this->cur_page+1)>$this->pages)
        {
            $this->nextpage = $this->pages;
        }
        else
        {
            $this->nextpage = $this->cur_page+1;
        }
        if(($this->cur_page-1)<1)
        {
            $this->prepage = 1;
        }
        else
        {
            $this->prepage = $this->cur_page-1;
        }
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


    function pagebar($suffix = '',$pagegoto=true)
    {
        $text = '每页显示'.$this->pagesize.'条,共'.$this->total.'条';

        if ($this->total  > $this->pagesize) 
        {
            $text .= '&nbsp;当前页数:'.$this->cur_page.' / '.$this->pages;

            if ($this->cur_page > 1) 
            {
                $text .= "&nbsp;<a title='首 页' href='".$this->baseLink."1{$suffix}'>首 页</a>&nbsp;|";
            }

            if ($this->cur_page > 1) {
                $pre = $this->cur_page-1;
                $text .= "&nbsp;<a title='上一页' href='".$this->baseLink."{$pre}{$suffix}'>上一页</a>&nbsp;|";
            }
            if ($this->cur_page < $this->pages){
                $next = $this->cur_page+1;
                $text .= "&nbsp;<a title='下一页' href='".$this->baseLink."{$next}{$suffix}'>下一页</a>&nbsp;|";
            }
            if ($this->cur_page < $this->pages) {
                $text .= "&nbsp;<a title='尾 页' href='".$this->baseLink.$this->pages."{$suffix}'>尾 页</a>&nbsp;";
            }
             if($pagegoto==true && $text!='')
             {
                 $text .= $this->pagegoto();
                 $text .= $this->pagegotojs($suffix);
             }
         }
         return $text;
    }


    function pagebars($suffix = '',$pagegoto=true,$pernum = 10)
    {
        $text = '每页显示'.$this->pagesize.'条,共'.$this->total.'条';
        if ($this->total  > $this->pagesize) 
        {
            if ($this->cur_page > 1) 
            {
                $text .= "<a title='首 页' href='".$this->baseLink."1{$suffix}'><<</a>";
            }
            if ($this->cur_page > 1) {
                $pre = $this->cur_page-1;
                $text .= "<a title='上一页' href='".$this->baseLink.$this->prepage."{$suffix}'><</a>";
            }
            //start by rlby 
            $tem = $pernum - ($this->pages-$this->cur_page);
            if($tem>0)
            {
                for($j=1; $j<$tem; $j++)
                {
                    if(($this->cur_page-$tem+$j)>0)
                    {
                        $text .= "<a href='".$this->baseLink.($this->cur_page-$tem+$j)."{$suffix}'>[".($this->cur_page-$tem+$j)."]</a>";
                    }
                }
            }
            //end
            for($j=0; $j<$pernum; $j++)
            {
                if ($j==0)
                {
                    $text .= $this->cur_page.'';
                } 
                else
                {
                    if(($this->cur_page+$j)<=$this->pages)
                    {
                        $text .= "<a href='".$this->baseLink.($this->cur_page+$j)."{$suffix}'>[".($this->cur_page+$j)."]</a>";
                    }
                    else
                    {
                        break;
                    }

                }
            }            
            if ($this->cur_page < $this->pages){
                $next = $this->cur_page+1;
                $text .= "<a title='下一页' href='".$this->baseLink.$this->nextpage."{$suffix}'>></a>";
            }
            if ($this->cur_page < $this->pages) {
                $text .= "<a title='尾 页' href='".$this->baseLink.$this->pages."{$suffix}'>>></a>";
            }
         }

         if($pagegoto==true && $text!='')
         {
             $text .= $this->pagegoto();
             $text .= $this->pagegotojs($suffix);
         }
         return $text;
    }


    function pagegoto()
    {
          $text = '转到<input name="inputpage" id="inputpage" type="text" size="3" class="kd05"  onkeyup="value=value.replace(/[^\d]/g,\'\')"/>页<input name="Submit4" type="button" value="转" onclick="pagegoto(\''.$this->baseLink.'\');" />';
          return $text;
    }


    function pagegotojs($suffix = '')
    {
        $js = "<script>function pagegoto(url){page=document.getElementById('inputpage').value; var parten = /[0-9]/; 
    if(parten.exec(page)){window.location.href=url+page+'".$suffix."';}else{ showalert('您输入的页码错误!'); }}</script>";
        return $js;
    }



}  

// END Pagination Class

/* End of file Pagination.php */
/* Location: ./system/libraries/Pagination.php */
