# web-app-internet-app

todo: 
- la recomendación no interaccionará con el php. Es decir, nosotros haremos un fetch a la base de datos donde la base de datos estará ordenada ya con nuestro algoritmo. 
Matlab se encargará de editar la tabla de peliculas segun la recomendacion (O crear una nueva tabla con recomendaciones o no se)
- la funcion quitar fecha es porque sino no hace bien la llamada a la api. de momento hay conflictos con las peliculas que tengan () en su nombre. 

## notas BBDD

Se supone que hay un servidor implementado que corre peticiones de matlab en tcp:1111.
**Cosas que disponemos**:
- Un script que lo conecta con php (**conectar.php**) donde tenemos que enviarle la ruta de nuestros archivos .m **y la funcion a ejecutar**, entiendo que dentro de esa funcion se podrá ejecutar otras
-  Dos scripts de matlab: **getData()** y **updateRecommendation()**. La primera genera las matrices R,Y y movieList. La segunda, mete en la tabla **recs** lo que necesitamos.

La idea es hacer recomendar($userid) a partir de los datos que podemos obtener, meter nuestro algoritmo, y simplemente pasarle el resultado a updateRecommendation. Luego en la parte visual de php, hay que trabajar con la tabla recs. (Recs es de puntuaciones que obviamente son predicciones, para puntuaciones **hechas** por el usuario está la tabla user_score)
