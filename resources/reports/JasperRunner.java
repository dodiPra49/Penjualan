package reports;

import net.sf.jasperreports.engine.*;
import net.sf.jasperreports.engine.data.JsonDataSource;
import net.sf.jasperreports.engine.export.JRCsvExporter;
import net.sf.jasperreports.engine.export.ooxml.JRXlsxExporter;
import net.sf.jasperreports.engine.util.JRLoader;
import net.sf.jasperreports.export.SimpleExporterInput;
import net.sf.jasperreports.export.SimpleOutputStreamExporterOutput;
import net.sf.jasperreports.export.SimpleWriterExporterOutput;

import java.io.File;
import java.io.FileOutputStream;
import java.sql.Connection;
import java.sql.DriverManager;
import java.util.HashMap;
import java.util.Map;
import java.util.Properties;

public class JasperRunner {
    public static void main(String[] args) {
        String input = null;
        String output = null;
        String format = "pdf";
        String dataFile = null;
        String dbUrl = null;
        String dbUser = "root";
        String dbPass = null;
        Map<String, Object> params = new HashMap<>();

        for (int i = 0; i < args.length; i++) {
            switch (args[i]) {
                case "-i":
                case "--input":
                    if (i + 1 < args.length) input = args[++i];
                    break;
                case "-o":
                case "--output":
                    if (i + 1 < args.length) output = args[++i];
                    break;
                case "-f":
                case "--format":
                    if (i + 1 < args.length) format = args[++i].toLowerCase();
                    break;
                case "-d":
                case "--data-file":
                    if (i + 1 < args.length) dataFile = args[++i];
                    break;
                case "--db-url":
                    if (i + 1 < args.length) dbUrl = args[++i];
                    break;
                case "--db-user":
                    if (i + 1 < args.length) dbUser = args[++i];
                    break;
                case "--db-pass":
                    if (i + 1 < args.length) {
                        String p = args[++i];
                        if (!p.isEmpty() && !"null".equalsIgnoreCase(p)) {
                            dbPass = p;
                        }
                    }
                    break;
                case "-P":
                    if (i + 1 < args.length) {
                        String paramStr = args[++i];
                        int eq = paramStr.indexOf('=');
                        if (eq > 0) {
                            String k = paramStr.substring(0, eq).trim();
                            String v = paramStr.substring(eq + 1).trim();
                            params.put(k, v);
                        }
                    }
                    break;
            }
        }

        if (input == null || output == null) {
            System.err.println("Usage: java reports.JasperRunner -i <input.jrxml|jasper> -o <output_file> -f <pdf|xlsx|csv|html> [-d <data.json>] [--db-url <jdbc_url>] [-P key=value ...]");
            System.exit(1);
        }

        try {
            JasperReport jasperReport;
            if (input.endsWith(".jrxml")) {
                jasperReport = JasperCompileManager.compileReport(input);
            } else {
                jasperReport = (JasperReport) JRLoader.loadObject(new File(input));
            }

            JasperPrint jasperPrint;

            if (dataFile != null && new File(dataFile).exists()) {
                JsonDataSource ds = new JsonDataSource(new File(dataFile));
                jasperPrint = JasperFillManager.fillReport(jasperReport, params, ds);
            } else if (dbUrl != null && !dbUrl.isEmpty()) {
                Properties props = new Properties();
                props.put("user", dbUser);
                if (dbPass != null) {
                    props.put("password", dbPass);
                }
                try (Connection conn = DriverManager.getConnection(dbUrl, props)) {
                    jasperPrint = JasperFillManager.fillReport(jasperReport, params, conn);
                }
            } else {
                jasperPrint = JasperFillManager.fillReport(jasperReport, params, new JREmptyDataSource());
            }

            String finalOutput = output;
            if (!finalOutput.toLowerCase().endsWith("." + format)) {
                finalOutput += "." + format;
            }

            File targetFile = new File(finalOutput);
            if (targetFile.getParentFile() != null && !targetFile.getParentFile().exists()) {
                targetFile.getParentFile().mkdirs();
            }

            if ("pdf".equals(format)) {
                JasperExportManager.exportReportToPdfFile(jasperPrint, finalOutput);
            } else if ("html".equals(format)) {
                JasperExportManager.exportReportToHtmlFile(jasperPrint, finalOutput);
            } else if ("xlsx".equals(format)) {
                JRXlsxExporter exporter = new JRXlsxExporter();
                exporter.setExporterInput(new SimpleExporterInput(jasperPrint));
                exporter.setExporterOutput(new SimpleOutputStreamExporterOutput(new FileOutputStream(targetFile)));
                exporter.exportReport();
            } else if ("csv".equals(format)) {
                JRCsvExporter exporter = new JRCsvExporter();
                exporter.setExporterInput(new SimpleExporterInput(jasperPrint));
                exporter.setExporterOutput(new SimpleWriterExporterOutput(targetFile, "UTF-8"));
                exporter.exportReport();
            } else {
                JasperExportManager.exportReportToPdfFile(jasperPrint, finalOutput);
            }

            System.out.println("[SUCCESS] Report generated: " + finalOutput);
            System.exit(0);
        } catch (Exception e) {
            e.printStackTrace();
            System.exit(2);
        }
    }
}
