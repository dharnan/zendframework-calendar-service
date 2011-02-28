<?php
/**
 * @author Arietis Software
 * @copyright 2011 Arietis Software
 * @license http://www.arietis-software.com/license/gnu/license.txt
 */
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>Demo | Zend Calendar Service</title>
        <link href="global/css/global.css" rel="stylesheet" type="text/css" media="all" />
        <link rel="stylesheet" type="text/css" href="/css/calendar.css" />
        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js"></script>        
        <script type="text/javascript" src="/scripts/jquery.jcarousel.min.js"></script>
        <script type="text/javascript">
            $(document).ready(function(){
                $("ul#monthCarousel").jcarousel({
                    scroll: 3,
                    visible: 3,
                    start: Math.min($('li#selected-month').index(),$('ul#monthCarousel > li').length - 1),
                    animation: 800
                });
            });
        </script>
    </head>
    <body>                    
    	<div id="calendarWrapper">
        	<div id="calendarHeader">
            	<div id="scrollableWrapper">
                	<ul id="monthCarousel" class="jcarousel-skin-custom">
                    	<?php foreach ($this->calHeader as $arr) : ?>
                       	<li id="<?php echo $arr['id']; ?>">
                        	<a href="<?php echo $this->url($arr['url']); ?>"><?php echo $arr['text']; ?></a>
						</li>
                        <?php endforeach; ?>
                    </ul>
				</div>
			</div>
            <div id="calendarBody">
            	<table id="calendarTable" border="0" cellpadding="0" cellspacing="0">
                	<tr class="weekdays">
                    <?php foreach ($this->calWeekdays as $arr) : ?>
                    	<td class="<?php echo $arr['class']; ?>"><?php echo $arr['dayShortStr']; ?></td>
					<?php endforeach; ?>
                    </tr>
                    <?php foreach ($this->calMonthDays as $weekNum => $weekArr) : ?>
                    <tr class="days">
                    <?php foreach ($weekArr as $dayArr) : ?>
                    	<td class="<?php echo $dayArr['class']; ?>">
                        <?php if (isset($dayArr['num'])) : ?>
                        	<span class="dayNum"><?php echo $dayArr['num']; ?></span>
						<?php endif; ?>
                        </td>
                        <?php endforeach; ?>
					</tr>
                    <?php endforeach; ?>
				</table>
			</div>
		</div>
    </body>
</html>