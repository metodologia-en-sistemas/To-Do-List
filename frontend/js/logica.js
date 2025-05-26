
document.getElementById('buscador-tarea').addEventListener('input', function() {
    const filtro = this.value.toLowerCase();
    document.querySelectorAll('.community-task').forEach(function(card) {
        const titulo = card.getAttribute('data-titulo');
        const descripcion = card.getAttribute('data-descripcion');
        if (titulo.includes(filtro) || descripcion.includes(filtro)) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
});
