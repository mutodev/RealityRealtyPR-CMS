<h3 class="widgettitle"><label for="s">Buscar</label></h3>

<form id="searchform" method="get" action="/blog/">
	<div>
		<input id="s" name="s" type="text" value="Para buscar, escribe y aprieta enter" onfocus="if (this.value == 'Para buscar, escribe y aprieta enter') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Para buscar, escribe y aprieta enter';}" size="32" tabindex="1">
		<input id="searchsubmit" name="searchsubmit" type="submit" value="Search" tabindex="2">
	</div>
</form>