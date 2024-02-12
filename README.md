# Blog-Website

## Description
This project was created as a part of my Master's of Software development.
It was done after we were initially introduce to Html, Css, PHP, Javascript, and SQL.

Users can create, read, and edit articles.
The input text is parsed, so they cannot insert html code.

The users can also filter articles by selecting the appropriate tags.
NOTE: that if they choose to filter by multiple articles it will display the articles with ANY of the tags they selected.

They can also update the tags that a article has. The page that does this allows for the user to create new tags (with some error checking). When the user selects another article to change tags, these tags will persist (despite the page technically changing).
The page technically changes so the tags that an article currently has will already be ticked.

There is also an additional secret feature that appears when the screen is below a certain width.

## Installion
Install a Xampp server that includes PHP, and mySQL. Add the contents of the repository to the relevant folder for the server. 
Finally, import the blogs.sql file into the mySQL database service. Note that you may want to also delete all of the blogs from the mySQL database.

## Project Status
The project has been finished and closed.