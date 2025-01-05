function num_updated =  updateRecommendation(my_predictions,userid)
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

%Ponga el mombre de su base de datos al final 
url = 'jdbc:mysql://localhost:3306/ai0';
%Ponga su usuario y contraseña
user = 'ai0';
password = '';

con = DriverManager.getConnection(url, user, password);
%Preparamos para multiples inserciones
con.setAutoCommit(false);
%Preparamos una consulta parametrizada. Use el nombre de su tabla y la estructura adecuada para su tabla
query='INSERT INTO recs (user_id, movie_id, rec_score, time) VALUES(?, ?, ?,NOW()) ON DUPLICATE KEY UPDATE  rec_score=VALUES(rec_score)'
st = con.prepareStatement(query);
%Añadimos lotes
for i=1:length(my_predictions)
    st.setInt(1,userid);
    st.setInt(2,i);
    st.setDouble(3,my_predictions(i));
    st.addBatch();
end
%Ejecutamos consulta
num_updated=st.executeBatch()
con.commit();
try st.close(); catch, end

%Cerramos

try con.closeAllStatements(); catch, end
try con.close(); catch, end
end


