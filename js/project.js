/*TODO: Implement a method to allow for the editing of polygons.
*/
/*This removed the currently active polygon from the map.
*/
function removePolygon()
{
	if (typeof activePolygon != 'undefined')
	{
		activePolygon.setMap(null);
	}
	else
	{
		alert("Active Polygon is null");
	}
}

/*This saves the activePolygon to the database upon completion.
*/
function saveRegion(regionName,regionDescription)
{
		
		//Make the region object.
		var region = new Region(activePolygon,"Admin");
		region.setName(regionName);
		region.setDescription(regionDescription);
		
		//debug setting the name and description.
		alert("Region Name: " + region.getName());
		alert("Region Description: " + region.getDescription());
		//Add the region object to the global list of region objects.
		
		//Send the region object information off to the server to save to the database.
		
		//Retreive the region id for this region.
		//region.setID();
}


