package xyz.capybara.clamav;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.EnableAutoConfiguration;
import org.springframework.boot.context.embedded.MultipartConfigFactory;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.Configuration;

import javax.servlet.MultipartConfigElement;
import java.util.HashMap;
import java.util.Map;

@Configuration
@EnableAutoConfiguration
@ComponentScan
/**
 * Simple Spring Boot application which acts as a REST endpoint for
 * clamd server.
 */
public class Application {

  @Value("${clamd.maxfilesize}")
  private String maxfilesize;

  @Value("${clamd.maxrequestsize}")
  private String maxrequestsize;

  @Bean
  MultipartConfigElement multipartConfigElement() {
    MultipartConfigFactory factory = new MultipartConfigFactory();
    factory.setMaxFileSize(maxfilesize);
    factory.setMaxRequestSize(maxrequestsize);
    return factory.createMultipartConfig();
  }

  public static void main(String[] args) {
    SpringApplication app = new SpringApplication(Application.class);
    Map<String, Object> defaults = new HashMap<String, Object>();
    defaults.put("clamd.host", "192.168.50.72");
    defaults.put("clamd.port", 3310);
    defaults.put("clamd.timeout", 500);
    defaults.put("clamd.maxfilesize", "100MB");
    defaults.put("clamd.maxrequestsize", "100MB");

    StringBuilder stringBuilder = new StringBuilder();
    stringBuilder.append("http://");
    stringBuilder.append(defaults.get("clamd.host"));
    stringBuilder.append(defaults.get(":"));
    stringBuilder.append(defaults.get("clamd.port"));
    String clamdUri = stringBuilder.toString();

    defaults.put("clamd.uri", clamdUri);

    app.setDefaultProperties(defaults);
    app.run(args);
  }
}
