export function imageDownloader($distance, $id_image, $table) {
    fetch( $distance + 'images/imageDownloader.php?id_image=' + $id_image + "&table=" + $table)
        .then(response => {
            if(response.headers.get('Content-Type').split(';')[0] != 'text/html') { 
            const contentType = response.headers.get('Content-Type').split(';')[0];
            return response.blob().then(blob => {
                const blobUrl = URL.createObjectURL(blob);
                const downloadLink = document.createElement('a');
                downloadLink.href = blobUrl;
                downloadLink.download = 'nome_do_arquivo'; 
                downloadLink.click();
                URL.revokeObjectURL(blobUrl);
                })
            }
            else {
                alert('Erro ao localizar o documento');
            }
        })
        .catch(error => {
            console.error('Erro ao obter arquivo BLOB:', error);
        });
};