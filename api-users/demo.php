<div>
    <h1>Demo</h1>

    <form action="http://localhost:8000/api/products/register" method="post">
        <input type="text" name="nombre" placeholder="Nombre">
        <input type="number" name="precio" placeholder="Precio">
        <input type="number" name="stock" placeholder="Stock">
        <button type="submit">Guardar</button>
    </form>

    <?php 
       $products = json_decode(file_get_contents('http://localhost:8000/api/products'), true);

       foreach($products as $producto){
          echo "<p> {$producto['nombre']} - {$producto['precio']} - {$producto['stock']} </p>";
       }
     ?>
    </php?>
</div>