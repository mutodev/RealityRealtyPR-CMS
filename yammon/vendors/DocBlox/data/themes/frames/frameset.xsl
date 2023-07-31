<?xml version="1.0"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output indent="yes" method="html" />

  <xsl:template match="/">
  <html xmlns="http://www.w3.org/1999/xhtml">
    <head>
      <title><xsl:value-of select="$title" /></title>
      <meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
      <link type="text/css" href="css/default.css" rel="stylesheet" />
      <link type="text/css" href="css/theme.css" rel="stylesheet" />
    </head>
    <frameset cols="*, 980, *" frameborder="no" framespacing="0" border="0">
      <frame src="side.html" />
      <frameset rows="70, 32, *" frameborder="no" framespacing="0" border="0">
        <frame name="header" src="header.html" scrolling="no" />
        <frame name="menu" src="menu.html" scrolling="no" />
        <frameset cols="300, *" frameborder="no" framespacing="0" border="0">
          <frame name="sidebar" src="sidebar.html"/>
          <frame name="content" src="content.html"/>
        </frameset>
      </frameset>
      <frame src="side.html" />
    </frameset>
  </html>
  </xsl:template>

</xsl:stylesheet>