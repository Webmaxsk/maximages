<%-- This template is based on zub foundation's block grid --%>
<%-- Nondefault example: <% include MaxImagesBlockGrid classes="small-block-grid-5",noBorder=true %> --%>
<% if SortedImages %>
$addEffect("fancybox","gallery")
	<ul class="<% if classes %>$classes<% else %>small-block-grid-3<% end_if %>">
	        <% loop SortedImages %>
	        <li>
	            <a class="gallery<% if not noBorder %> th<% end_if %>" href="$Full.Link" data-fancybox-group="gallery-$Top.ID" title="$Title">
	                <img src="$Thumb.Link">
	            </a>
	        </li>
	        <% end_loop %>
	</ul>
<% end_if %>