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
		
		//Make the region object. This is currently working with it being a universal region by default. Later there will need to be a way to
		//get the user.
		var region = new Region(activePolygon,"Admin", "universal");
		region.setName(regionName);
		region.setDescription(regionDescription);
		
		
		//Add the region object to the global list of region objects.
		
		//Send the region object information off to the server to save to the database.
		
		//Retrieve the region id for this region.
		//region.setID();
}


