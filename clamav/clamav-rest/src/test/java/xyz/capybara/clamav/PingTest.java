package xyz.capybara.clamav;

import static org.junit.Assert.assertEquals;

import org.junit.Test;
import org.springframework.web.client.RestTemplate;
import org.springframework.http.ResponseEntity;
import org.springframework.http.HttpStatus;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.EnableAutoConfiguration;
import org.springframework.boot.context.embedded.MultipartConfigFactory;
import org.springframework.context.annotation.ComponentScan;
import org.springframework.context.annotation.Configuration;

/**
 * These tests assume clamav-rest Docker container is running and responding locally.
 */
@Configuration
public class PingTest {

  // @todo find out how to get the Configuration.
  @Value("${clamd.uri}")
  private String uri;

  @Test
  public void testPing() {
//    RestTemplate rt = new RestTemplate();
//    ResponseEntity<Object> response = rt.getForObject("http://localhost:8000", ResponseEntity.class);
//    assertEquals(response.getStatusCode(), (int) 200);
  }
}
