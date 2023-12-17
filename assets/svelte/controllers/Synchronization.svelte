<script>
    import { onMount } from 'svelte';

    let rate = '88';
    let store = 'All';
    let company = '01';
    let currentPercentage = 0;

    onMount(async () => {
		const res = await fetch(`shop/data`);
		let data = await res.json();
        rate = data['rate'];
        store = data['store'];
        console.log(data);
	});

    async function synchronize() {
        const response = await fetch('synchronize', {
            method: 'POST',
            headers: {
                'Content-Type': 'text/event-stream'
            },
            body: JSON.stringify({
                     rate,
                     store,
                     company
            })
        })

        const jsonData = await toJSON(response.body);
    }

    async function toJSON(body) {
        const reader = body.getReader(); // `ReadableStreamDefaultReader`
        const decoder = new TextDecoder();
        // const chunks = [];

        async function read() {
            const { done, value } = await reader.read();

            // all chunks have been read?
            if (done) {
                //return JSON.parse(chunks.join(''));
                return [];
            }

            const chunk = decoder.decode(value, { stream: true });
            //chunks.push(chunk);

            const regexFront = /^[{\"\w:\d}]*,/i;
            const replacedChunkAtTheStart = chunk.replace(regexFront, '[[');

            const regexEnd = /,[{\"\w:\d}]*$/i;
            const replacedChunkAtTheEnd = replacedChunkAtTheStart.replace(regexEnd, ']]');

            let chunks = JSON.parse(replacedChunkAtTheEnd)[0];
            const filteredChunks = deleteRepetitiveValues(chunks);

            filteredChunks.forEach(({percentage}) => {
                currentPercentage = percentage;
            });
            
            return read(); // read the next chunk
        }

        return read();
    }

    function deleteRepetitiveValues(array) {
        // Create a new array to store the unique values
        var newArray = [];
        // Loop through the array of objects
        for (var i = 0; i < array.length; i++) {
            // Get the current object
            var obj = array[i];
            // Get the percentage value of the object
            var percentage = obj.percentage;
            // Check if the percentage value is already in the new array
            var found = false;
            for (var j = 0; j < newArray.length; j++) {
            // Get the percentage value of the new array object
            var newPercentage = newArray[j].percentage;
            // Compare the percentage values
            if (percentage === newPercentage) {
                // Set the found flag to true
                found = true;
                // Break the inner loop
                break;
            }
            }
            // If the percentage value is not found in the new array, push the object to the new array
            if (!found) {
            newArray.push(obj);
            }
        }
        // Return the new array
        return newArray;
    }
</script>

<style>
    .block-separtor {
        border-bottom: 1px solid rgba(26, 54, 126, 0.125);
    }

    .space-between-blocks {
        padding: 1rem 0rem;
    }

    .synchronisation-composite-card {
        display: flex;
        padding: 0rem;
        flex-direction: column;
        background-color: #fdfdfd;
        border-radius: 0.25rem;
        box-shadow: 0 0.46875rem 2.1875rem rgb(4 9 20 / 3%), 0 0.9375rem 1.40625rem rgb(4 9 20 / 3%), 0 0.25rem 0.53125rem rgb(4 9 20 / 5%), 0 0.125rem 0.1875rem rgb(4 9 20 / 3%);
        margin-bottom: 1rem;
    }

    .initial-text-container__initial-text {
        font-size: 1.5em;
        font-weight: bold;
        font-family: math;
        color: #af7643;
    }

    .progress-bar-percentage__container {
        display: flex;
    }

   .button {
        background:#34b673;
        color: white;
        border: 0;
        cursor: pointer;
        padding: 0.5rem 1rem;
        border-radius: 0.25rem;
    }

    .button[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }

</style>
<div class="synchronisation-composite-card">
    <div class="composite-card__information">
        <div class="composite-card__header block-separtor">
            <i class="fa-brands fa-product-hunt synchronisation-icon"></i>
            <div class="server-card__container-name">
                <span class="server-card__name">Sincronizar productos</span>
            </div>
        </div>

        <div class="composite-card__body block-separtor">
            <div class="server-card__list">
                <span class="server-card__item synchronisation-card__folder">Nota: <b class="server-card__item-value">Los productos corresponse a la tabla de articulos de la base de datos de Eurowin</b></span>
                <div class="server-card__item-container">
                    <span class="server-card__item synchronisation-card__folder synchronisation-info-articles">Tarifa: </span>
                    <b class="server-card__item-value">{rate}</b>
                </div>
                <div class="server-card__item-container">
                    <span class="server-card__item synchronisation-card__folder synchronisation-info-articles">Almacen: </span>
                    <b class="server-card__item-value">{store}</b>
                </div>
                <!-- <div class="server-card__item-container">
                    <span class="server-card__item synchronisation-card__folder synchronisation-info-articles">Total de articulos: </span>
                    <b class="server-card__item-value">6536</b>
                </div> -->
                
                {#if currentPercentage >= 1 && currentPercentage <= 99 || currentPercentage === 100}
                    <div class="space-between-blocks">
                        <h2 class="progress-bar__title">Progreso de sincronización</h2>
                        <div class="progress-bar-percentage__container">
                            <div class="progress-bar__container">
                                <div class="progress-bar" style="transform: translate({currentPercentage}%, 0px)">
                                    {#if currentPercentage === 100}
                                        <span class="progress-bar__text">Completado!</span>
                                    {/if}
                                </div>
                            </div>
    
                            <span class="progress-bar__percentage">{currentPercentage}%</span>
                        </div>
                    </div>
                {:else}
                    <div class="space-between-blocks initial-text-container">
                        <span class="initial-text-container__initial-text">Presione el boton de sincronización para comenzar</span>
                    </div>
                    <!-- <div class="synchronisation-status">
                        <h3 class="synchronisation-status__title">Cargando datos</h3>
                        <div class="linear-progress-material">
                            <div class="bar bar1"></div>
                            <div class="bar bar2"></div>
                        </div>
                        <span class="server-card__item synchronisation-card__folder synchronisation-status__message">Este proceso puede tardar varios minutos.</span>
                    </div> -->
                {/if}
 
            </div>
        </div>

        <div style="display:flex;">
            <div class="composite-card__footer">
                <button class="button" disabled='{currentPercentage >= 1 && currentPercentage <= 99}' on:click={synchronize}>Sincronizar</button>
            </div>
        </div>
    </div>
</div>
