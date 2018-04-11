<%-- This template is based on zub foundation's cleaing box --%>
<%-- Nondefault example: <% include MaxImagesOrbit noBorder=true,showOnlyOne=true %> --%>
<% if SortedImages %>
	<ul class="clearing-thumbs<% if $showOnlyOne %> clearing-feature<% end_if %>" data-clearing>
	        <% loop SortedImages %>
	        <li<% if showOnlyOne && First %> class="clearing-featured-img"<% end_if %>>
	            <a <% if not noBorder %>class="th" <% end_if %>href="$Full.Link">
	                <img data-caption="$Title" src="$CroppedImage(182,100).Link">
	            </a>
	        </li>
	        <% end_loop %>
	</ul>
<% end_if %>