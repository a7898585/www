<?php

//生成翻页地址列表
function drawpagebar($cnt, $page, $pagesize, $baselink, $leftnum = 4, $rightnum = 1, $prefix = '.html') {
    $totlapage = ceil($cnt / $pagesize);
    $barStr = '<div class="PageInfo fl">(每页<span>' . $pagesize . '</span>条,当前页: <span>' . $page . '</span>/' . $totlapage . ')</div>';

    if ($totlapage < 2)
        return $barStr;

    $barStr .= '<div class="Pagebar fr">';
    $barStr .= '<a href="' . $baselink . '1' . $prefix . '">&lt;&lt;</a>';
    if ($totlapage < ($leftnum + $rightnum)) {
        $barStr .= '<span class="disabled">&lt;</span>';
        for ($i = 1; $i <= $totlapage; $i++) {
            if ($page == $i) {
                $barStr .= '<span class="page_now">' . $i . '</span>';
                continue;
            }
            $pageurl = $baselink . $i . $prefix;
            $barStr .= '<a href="' . $pageurl . '">' . $i . '</a>';
        }
        $barStr .= '<span class="disabled">&gt;</span>';
    } else {
        if ($page > 1) {
            $pageurl = $baselink . ($page - 1) . $prefix;
            $barStr .= '<a href="' . $pageurl . '">&lt;</a>';
        } else {
            $barStr .= '<span class="disabled">&lt;</span>';
        }

        $halfpnum = intval($leftnum / 2);

        $prebegin = (($page - $halfpnum) > 0) ? ($page - $halfpnum) : 1;
        $endpage = $prebegin + ($halfpnum * 2);
        $splite = '';
        if ($endpage >= ($totlapage - $rightnum)) {
            $last = $endpage - ($totlapage - $rightnum);
            $endpage -= ($last + 1);
            $prebegin -= ($last + 1);
        } else {
            $splite = '...';
        }
        for ($i = $prebegin; $i <= $endpage; $i++) {
            if ($page == $i) {
                $barStr .= '<span class="page_now">' . $i . '</span>';
                continue;
            }
            $pageurl = $baselink . $i . $prefix;
            $barStr .= '<a href="' . $pageurl . '">' . $i . '</a>';
        }

        $barStr .= $splite;

        for ($i = ($totlapage - $rightnum); $i <= $totlapage; $i++) {
            if ($page == $i) {
                $barStr .= '<span class="page_now">' . $i . '</span>';
                continue;
            }
            $pageurl = $baselink . $i . $prefix;
            $barStr .= '<a href="' . $pageurl . '">' . $i . '</a>';
        }

        if ($page < $totlapage) {
            $pageurl = $baselink . ($page + 1) . $prefix;
            $barStr .= '<a href="' . $pageurl . '">&gt;</a>';
        } else {
            $barStr .= '<span class="disabled">&gt;</span>';
        }
    }
    $barStr .= '<a href="' . $baselink . $totlapage . $prefix . '">&gt;&gt;</a>';
    $barStr .= '</div>';

    return $barStr;
}

?>
