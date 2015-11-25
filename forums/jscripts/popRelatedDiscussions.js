
function populateRelatedDiscussions(data){

	// The maximum number of posts to read through.
	var maxPostCount = 200;
	
	// The maximum and minimum view counts for related posts.
	var maxViews = 0;
	var minViews = 0;
	
	// The maximum and minimum scores for related posts.
	var maxScore = 0;
	var minScore = 0;
	
	// The maximum and minimum number of related tags possessed by related posts.
	var maxTagCount = 0;
	var minTagCount = 0;
	
	// The number of related tags possessed by each post.
	var tagCount = [];
	
	var relatedTags = ["java", "parameter", "jml", "specification", "spec", "preconditions", "post-conditions", "design-by-contract", "assert", "assertions", "assertion"];
	
	$.each( data.items, function( i, item ){
		
		// End the function (and therefore the loop) if we have max posts
		if( i == maxPostCount ){
			return false;
		}
		
		// Get maximum and minimum Views.
		if( i == 0 ){
			maxViews = item.view_count;
			minViews = item.view_count;
		}
		else if( item.view_count > maxViews ){
			maxViews = item.view_count;
		}
		else if( item.view_count < minViews ){
			minViews = item.view_count;
		}
		
		// Get the maximum and minimum Scores
		if( i == 0 ){
			maxScore = item.score;
			minScore = item.score;
		}
		else if( item.score > maxScore ){
			maxScore = item.score;
		}
		else if( item.score < minScore ){
			minScore = item.score;
		}
		
		// Get the tag count
		var numTags = 0;
		$.each( item.tags, function( j, tag ){
			
			for( k = 0; k < relatedTags.length; k++ ){
				if( tag == relatedTags[k] ){
					numTags++;
				}
			}
		});
		
		tagCount[tagCount.length] = numTags;
		
		if( i == 0 ){
			maxTagCount = numTags;
			minTagCount = numTags;
		}
		else if( numTags > maxTagCount ){
			maxTagCount = numTags;
		}
		else if( numTags < minTagCount ){
			minTagCount = numTags;
		}
		
	});
	
	var rankedWeight = [];
	
	$.each( data.items, function( i, item ){
		
		// End the function (and therefore the loop) if we have max posts
		if( i == maxPostCount ){
			return false;
		}
		
		var rScore = ( item.score - minScore ) / ( maxScore - minScore );
		var rViews = ( item.view_count - minViews ) / ( maxViews - minViews );
		var rTagCount = ( tagCount[i] - minTagCount ) / ( maxTagCount - minTagCount );
		rankedWeight[i] = rScore + rViews + rTagCount;
	});
	
	console.log(rankedWeight);
	//return rankedWeight;
	return sortedIndices( rankedWeight );
}

function sortedIndices( arr ){
	
	var indices = [];
	
	for( i = 0; i < arr.length; i++ ){
		indices[i] = i;
	}
	
	for( i = 0; i < arr.length; i++ ){
		for( j = i+1; j < arr.length; j++ ){
			
			if( arr[j] > arr[i] ){
				
				var tmp = arr[i];
				arr[i] = arr[j];
				arr[j] = tmp;
				
				tmp = indices[i]
				indices[i] = indices[j];
				indices[j] = tmp;
			}
		}
	}
	
	return indices;
}