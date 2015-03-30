/**This class will be responsible for holding the information for the regions on the client side.
This will be useful for us because it will allow us to more easily update,save,and delete the regions as 
well as add and remove them from the map.

This class also makes it possible to update the region in the database without needing to iterate over all of the polygon points, and attached the
polygon id to the polygon. 

IDEA: It would be nice to attach listeners to this class to implicitly handle the saving, updating, and removal
	of its polygon.

Possible Issue:  this file depends on the google polygon api, so it is possible that it may not load correctly unless we follow the solution in this link.
http://stackoverflow.com/questions/4634644/how-to-include-js-file-in-another-js-file
**/

/** defines the class region. You can create an instance of region using
expected syntax. var region = new Region(polygon,owner)
param : polygon -> The polygon that this region will contain.
param : owner -> The owner of this region.

NOTE: polygon and owner cannot be changed after creation.
**/

function Region (polygon, owner)
{
	//The google api polygon that contains a list of points that enclose an area
	//Documentation: https://developers.google.com/maps/documentation/javascript/reference#Polygon
	this.polygon_ = polygon;
	
	//The owner of the region. 
	//Universal -> Everyone
	//master -> practice account.
	this.owner_ = owner;
	this.type_ = null;
	if (this.owner_ === "universal")
	{
		this.type_ = "universal";
	}
	else
	{
		this.type_ = "personal";
	}
	
	//The ID of this region. This should be set to null until saved to the database.
	this.regionID_ = null;
	
	this.name_ = null;
	this.description_ = null;
	
	//whether or not the polygon is currently being displayed on the map.
	this.active_ = false;
	
	//holds information on whether the polygon was changed and the new region needs to be saved.
	//I don't think this style is good enough, because the polygon could be changed outside of this class.
	//As a result, ensuring the appropriate methods are called outside of this class may be a better solution.
	//this.hasChanged_ = false;
	
	
	//What the polygon looks like when it is a universal type, also note that it is not editable.
	if (this.owner_ == "universal")
	{
		polygon.setOptions({
                        editable: false,
                        fillColor: 'RED',
						draggable: false,
						clickable: false,
                        strokeColor: 'BLACK'
                    });
	}
	
	//What the polygon looks like when it belongs to a user, note that it is editable.
	else
	{

		polygon.setOptions({
			editable: true,
			fillColor: 'BLUE',
			draggable: true,
			clickable: true,
			strokeColor: 'GREEN'
			
		});
	}
	
	
this.getType = function()
{
	return this.type_;
}

this.isActive = function()
{
	return this.active_;
}

this.setActive = function(activeState)
{
	this.active_ = activeState;
}

this.getPolygon = function()
{
	return this.polygon_;
}

this.getPolygonPath = function()
{
	return this.polygon_.getPath();
}

this.getOwner = function()
{
	return this.owner_;
}

this.getRegionID = function()
{
	return this.regionID_;
}

this.getName = function()
{
	return this.name_;
}

this.getDescription = function()
{
	return this.description_;
}

/**Checks to see if the given polygon is the same as the one that this region stores.
param : polygon -> The polygon to test for.
**/
this.containsPolygon = function(polygon)
{
	return (this.polygon_ == polygon);
}

this.setName = function(newName)
{
	this.name_ = newName;
}

this.setDescription = function(newDescription)
{
	this.description_ = newDescription;
}

/**This will only allow the setting of the region id if it is already null.
**/
this.newRegionID = function(newRegionID)
{
	if (this.regionID_ == null)
	{
		this.regionID_ = newRegionID;
	}
}


/**Adds the polygon to the map, and sets the active state of this region to true.
**/
this.addpolygonToMap = function()
{
	polygon.setVisible(true);
	this.active_ = true;
}

/**Removes the polygon from the map, and sets the active state of this region to false.
**/
this.addPolygonToMap = function()
{
	polygon.setVisible(false);
	this.active_ = false;
}

this.XMLHTTPString = function()
{
	var xmlString = "";
	
	xmlString = string + "&owner=" + this.owner_;
	xmlString = string + "&regionID=" + this.regionID_;
	xmlString = string + "&name=" + this.name_;
	xmlString = string + "&description=" + this.description_;
	
	//unsure of the best way to make the path into a string, here is a link to a poissible solution.
	//http://stackoverflow.com/questions/12096941/how-to-send-arrays-using-xmlhttprequest-to-server
}
/**Saves the polygon to the database
**/
this.savePolygon = function()
{
	//Do the magic saving thing.
	
	//Don't forget, we require the regionID of this thing after it has been saved. Somehow we need to get it.
	if (this.regionID_ == null)
	{
		alert("The region id was not returned upon the completion of the savePolygon() function of this region. Any changed in this region cannot be reflected in the database.");
	}
}

/**Update the region in the database.
**/
this.updatePolygon = function()
{
	//Do the magic update thing.
}

this.deletePolygon = function()
{
	//Do the magic deletion thing.
}
}