<?php

if ($data)
{
	// Ensure our IDs aren't printed as objects. When MongoIDs are json_encoded they 
	// print as { '$id' : $mongoid } instead of a string...
	array_walk_recursive($data, function(&$item, &$key) {
		if ($item instanceof MongoId)
		{
			$item = (string) $item;
		}
	});
}

echo json_encode($data);
