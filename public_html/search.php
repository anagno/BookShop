<?php

require_once "common.inc.php";
require_once "../php/Book.class.php";

$search_parameter = "";

if ( isset( $_GET["title"] ) )
{
	$search_parameter = strtoupper((string) $_GET["title"]);
	
	if($books= Book::search($search_parameter))
	{
		displayPageHeader( "Αποτελέσματα");
		?>
		
		<!-- https://datatables.net/examples/basic_init/alt_pagination.html -->
		<script src="http://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
		<script src="http://cdn.datatables.net/1.10.6/js/jquery.dataTables.min.js"></script>
		<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.6/css/jquery.dataTables.css"/>
		<script type="text/javaScript">
			$(document).ready(function() 
			{
    			$('#pagination').dataTable( 
    	    	{
        			"pagingType": "full_numbers"
    			});
			});		
		</script>
			
		<table id='pagination'>
		<thead>
		<tr><td> Τίτλος </td><td> Συγγραφείς </td> </tr>
		</thead>
		<tfoot>
		<tr><td> Τίτλος </td><td> Συγγραφείς </td> </tr>
		</tfoot>
		<tbody>
		
		<?php 
		foreach ($books as $book)
		{
			?>
			<tr><td>
			<a href="book.php?id=<?=$book->getValueEncoded('id');?>"><?=$book->getValueEncoded('title');?></a>
			</td><td>
			<?php 
			foreach ($book->getAuthors() as $author)
			{
				?>
				<a href="author.php?id=<?=$author->getValueEncoded("id");?>"><?=$author->getValueEncoded("name");?></a>  
				<?php 
				if( end($book->getAuthors()) !== $author)
				{
	    			echo ', '; // not the last element
				}
			}
			?>			
			</td> </tr>			
			<?php
		}
		?>

		</tbody>
		</table>
		
	<?php 
	}
	else 
	{
		displayPageHeader( "Δεν βρέθηκαν βιβλία");
	}
	?>
	
<?php 
}
?>

<?php
displayPageFooter();
?>