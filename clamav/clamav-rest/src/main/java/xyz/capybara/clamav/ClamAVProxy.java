package xyz.capybara.clamav;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.util.HashMap;
import java.util.Map;

import xyz.capybara.clamav.commands.scan.result.ScanResult;

@RestController
public class ClamAVProxy {

  @Value("${clamd.host}")
  private String hostname;

  @Value("${clamd.port}")
  private int port;

  /**
   * @return Clamd status.
   */
  @RequestMapping(value={"/", "/scan"}, method=RequestMethod.GET, produces="application/json")
  public @ResponseBody Map<String, Object> ping() throws IOException {
    ClamavClient a = new ClamavClient(hostname, port);

    Map map = new HashMap<String, Object>();

    map.put("version", a.version());

    return map;
  }

  @RequestMapping(value="/scan", method=RequestMethod.POST, produces="application/json")
  public @ResponseBody Map<String, Object> handleFileUpload(@RequestParam("name") String name,
                                                            @RequestParam("file") MultipartFile file) throws IOException {

    HashMap<String, Object> map = new HashMap<String, Object>();

    if (!file.isEmpty()) {
      ClamavClient a = new ClamavClient(hostname, port);
      ScanResult r = a.scan(file.getInputStream());

      map.put("file", r);
    } else {
      throw new IllegalArgumentException("empty file");
    }

    return map;

  }

}
