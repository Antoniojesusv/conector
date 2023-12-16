<script>
	let columns = ["Código", "Precio", "Stock", "Sincronizado", "Total de articulos"]

    async function fetchData() {
		const res = await fetch(`http://localhost/log/article`);
		const articles = await res.json();

		if (res.ok) {
			console.log(articles);
      		return articles;
		} else {
			throw new Error(articles);
		}
	};
</script>

{#await fetchData()}
<p>loading</p>
{:then articles}
<div class="table-container">
	<table class="table">
		<thead class="table__head">
			<tr>
				{#each columns as column}
					<th class="table__cell-head">{column}</th>
				{/each}
			</tr>
		</thead>
		<tbody class="table__body">
			{#each articles as row}
			<tr>
				<td class="table__cell">{row.id}</td>
				<td class="table__cell">{row.price}</td>
				<td class="table__cell">{row.stock}</td>
				<td class="table__cell">{row.synchronized}</td>
				<td class="table__cell">{row.totalArticle}</td>
			</tr>
		{/each}
		</tbody>
	</table>
</div>
{:catch error}
  <p style="color: red">Se ha producido un error con la recuperación de datos</p>
{/await}

<style>
	tr td:focus {
		background: #eee;
	}
</style>