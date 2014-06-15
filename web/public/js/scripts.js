var Vote = {

	sendVote : function(type, id, voteType)
	{
		var url = Routing.generate('info_complaint_vote',{'type':type,'id':id,'voteType':voteType}); 
		$.getJSON(url, function(data){

			if(!data.error)
			{
				if(type == 'complaint')
				{
					idValue = 'vote-complaint'+id;
					$("#"+idValue).html(data.voteValue);
				}else
				{
					idValue = 'vote-comment'+id;
					$("#"+idValue).html(data.voteValue);
				}
				
			}else
			{
				alert(data.errorType);
			}
		
		});
	}

}

