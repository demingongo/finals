<h2>{'My reservations'|trans}</h2>
<hr />

{foreach $app.user.data.reservations as $resa}
{if_not_expired from=$resa.expires_at}
{$total=0}
<div class="panel panel-default">
	<div class="panel-heading">
    	<div>Code reservation: {$resa.id}</div>
    	<div>Reservation: {$resa.created_at|date_format:"%Y-%m-%d %H:%M:%S"}</div>
        <div>Expiration: {$resa.expires_at|date_format:"%Y-%m-%d %H:%M:%S"}</div>
	</div>
    <div class="panel-body">
    	<div class="table-responsive">
        	<table id="tab" class="table table-striped table-hover">
            	<thead>
                	<th class="text-center">
                    	{'Title'|trans}
                    </th>
                    <th class="text-center">
                    	{'Unit price'|trans}
                    </th>
                    <th class="text-center">
                    	{'Quantity'|trans}
                    </th>
                    <th class="text-center">
                    	{'Subtotal'|trans}
                    </th>
                </thead>
                
                <tbody>
                	{foreach name=reservation_articles from=$resa.reservation_articles item=rA}
                    <tr>
                    	<td class="text-center">
                        	<a href="{path id=rgs_catalog_article_details params=['id' => $rA.article.id, 'slug' => $rA.article.slug]}">{$rA.article.name}</a>
                            <div>
                            	<img src="{image_src path=$rA.article.image package=upload}" 
                                class="img-thumbnail" alt="image" style="height: 120px; min-width: 120px;" />
                            </div>
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                        	{$rA.unitPrice} &euro;
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                        	{$rA.quantity}
                        </td>
                        <td class="text-center" style="vertical-align:middle;">
                        	<b>{$rA.unitPrice * $rA.quantity} &euro;</b>
                            {$total= $total + ($rA.unitPrice * $rA.quantity)}
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
{/if_not_expired}
{/foreach}
