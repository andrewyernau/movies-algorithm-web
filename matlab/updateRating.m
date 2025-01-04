function updateRatings(ratings, movieList)
    import java.sql.Connection;
    import java.sql.DriverManager;
    import java.sql.SQLException;

    url = 'jdbc:mysql://localhost:3306/ai0';
    user = 'ai0';
    password = '';
    con = DriverManager.getConnection(url, user, password);
    st = con.createStatement();
    for i = 1:length(movieList)
        query = sprintf('UPDATE movies SET rating = %.6f WHERE title = ''%s''', ...
        ratings(i), movieList{i});
        try
            st.executeUpdate(query);
        catch e
            fprintf('Error al actualizar la película: %s\n', movieList{i});
            disp(e.message);
        end
    end

    try st.close(); catch, end
    try con.closeAllStatements(); catch, end
    try con.close(); catch, end
end

