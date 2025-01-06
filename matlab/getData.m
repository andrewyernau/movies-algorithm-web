function [Y, R, movieList] = getData()
    import java.sql.Connection;
    import java.sql.DriverManager;
    import java.sql.ResultSet;
    import java.sql.SQLException;
    import java.sql.Statement;

    % Configuración de la conexión
    url = 'jdbc:mysql://localhost:3306/ai0';
    user = 'ai0';
    password = '';

    con = DriverManager.getConnection(url, user, password);

    % Obtenemos el número de películas y usuarios
    st = con.createStatement();
    rs = st.executeQuery('SELECT COUNT(id) FROM movie');
    rs.next();
    num_movies = rs.getInt(1);

    rs = st.executeQuery('SELECT COUNT(id) FROM users');
    rs.next();
    num_users = rs.getInt(1);

    try rs.close(); catch, end
    try st.close(); catch, end

    % Obtenemos la lista de películas
    st = con.createStatement();
    rs = st.executeQuery('SELECT title FROM movie ORDER BY id');
    
    movieList = cell(num_movies, 1);
    i = 1;
    while rs.next()
        movieList{i} = char(rs.getString(1));
        i = i + 1;
    end

    try rs.close(); catch, end
    try st.close(); catch, end

    % Inicializamos las matrices Y y R
    Y = zeros(num_movies, num_users);
    R = zeros(num_movies, num_users);

    % Obtenemos las puntuaciones y construimos las matrices Y y R
    st = con.createStatement();
    rs = st.executeQuery('SELECT id_user, id_movie, score FROM user_score');

    while rs.next()
        movie_id = rs.getInt(2);
        user_id = rs.getInt(1);
        Y(movie_id, user_id) = rs.getInt(3);
        R(movie_id, user_id) = 1;
    end

    % Cerramos las conexiones
    try rs.close(); catch, end
    try st.close(); catch, end
    try con.closeAllStatements(); catch, end
    try con.close(); catch, end
end