import java.sql.*;

public class ClusterTest {

    public static void main(String[] args) {
        Connection conn = null;
        String url = "jdbc:mysql:loadbalance://192.168.191.240:3306,192.168.191.111:3306/test?loadBalanceConnectionGroup=first&autoReconnect=true";
        String user = "test";
        String password = "test";

        try {
            Class.forName("com.mysql.cj.jdbc.Driver");
            conn = DriverManager.getConnection(url, user, password);

            // Retrieve the highest ID value from the database
            Statement stmtSelect = conn.createStatement();
            ResultSet rsSelect = stmtSelect.executeQuery(sqlSelect);
            int nextId = 1;
            if (rsSelect.next()) {
                nextId = rsSelect.getInt(1) + 1;
            }

            // Get the MySQL instance name
            String[] hosts = conn.getMetaData().getURL().split(",");
            String instanceName = hosts[0].contains("192.168.191.240") ? "Daniel's Database" : "Max's Database";
            System.out.println("Connected to: " + instanceName);

        } catch (ClassNotFoundException ex) {
            ex.printStackTrace();
        } catch (SQLException ex) {
            ex.printStackTrace();
        } finally {
            if (conn != null) {
                try {
                    conn.close();
                } catch (SQLException ex) {
                    ex.printStackTrace();
                }
            }
        }
    }
}
