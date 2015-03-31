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

/*This creates and saves a region to the database given the region's name and description.
	This also
 * @param - regionName ~ The region name to be saved.
 * @param - regionDescription ~ the region description.
 *
*/
function saveRegion(regionName,regionDescription)
{
		
		//Make the region object. This is currently working with it being a universal region by default. Later there will need to be a way to
		//get the user.
		//the active region is currently set in all of the listeners.
		var region = new Region(activePolygon,"Admin", "universal");
		region.setName(regionName);
		region.setDescription(regionDescription);
		
		
		//Add the region object to the global list of region objects.
		
		//Send the region object information off to the server to save to the database.
		saveRegionToDB("admin",region, function onSave(results){
			alert("Succeeded in saving to the database.");
			alert("Region id " + results);
			region.setID(results);
		});
		
		
		//Retrieve the region id for this region.
		//region.setID();
}


