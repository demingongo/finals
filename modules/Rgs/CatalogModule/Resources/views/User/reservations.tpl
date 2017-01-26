<h2>My reservations</h2>
<hr />

{foreach $app.user.data.reservations as $resa}
{$total=0}
<div class="panel panel-default">
	<div class="panel-heading">
    	Reservation: {$resa.created_at|date_format:"%Y-%m-%d %H:%M:%S"}
	</div>
    <div class="panel-body">
    	<div class="table-responsive">
        	<table id="tab" class="table table-striped table-hover">
            	<thead>
                	<th class="text-center">
                    	Titre
                    </th>
                    <th class="text-center">
                    	Prix à l'unité
                    </th>
                    <th class="text-center">
                    	Quantité
                    </th>
                    <th class="text-center">
                    	Sous-total
                    </th>
                </thead>
                
                <tbody>
                	{foreach name=reservation_articles from=$resa.reservation_articles item=rA}
                    <tr>
                    	<td class="text-center">
                        	<a href="#">{$rA.article.name}</a>
                            <div>
                            	<img src="{image_src path=$rA.article.image package=upload}" 
                                class="img-thumbnail" alt="image" style="height: 120px; min-width: 120px;" />
                            </div>
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                        	{$rA.prix_unitaire} &euro;
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                        	{$rA.quantite}
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                        	<b>{$rA.prix_unitaire * $rA.quantite} &euro;</b>
                            {$total= $total + ($rA.prix_unitaire * $rA.quantite)}
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
                
            </table>
        </div>
    </div>
    <div class="panel-footer">
    	<b class="">TOTAL: {$total} &euro;</b>
    </div>
</div>
{/foreach}
