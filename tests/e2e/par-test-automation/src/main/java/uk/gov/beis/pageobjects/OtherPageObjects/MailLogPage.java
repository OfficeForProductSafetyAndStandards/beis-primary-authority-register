package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;

import uk.gov.beis.enums.UsableValues;
import uk.gov.beis.helper.PropertiesUtil;
import uk.gov.beis.helper.ScenarioContext;
import uk.gov.beis.pageobjects.BasePageObject;
import uk.gov.beis.utility.DataStore;

public class MailLogPage extends BasePageObject {

	@FindBy(id = "edit-header-to")
	private WebElement toSearchBox;
	
	@FindBy(id = "edit-submit-maillog-overview")
	private WebElement applyBtn;
	
	@FindBy(name = "name")
	private WebElement username;
	
	private String email = "//tr/td[contains(normalize-space(),'?')]/preceding-sibling::td/a[contains(text(),'Invitation')]";
	
	public MailLogPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public void navigateToUrl() throws InterruptedException {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("maillog_url"));
	}
	
	public void searchForUserAccountInvite(String userEmail) {
		toSearchBox.sendKeys(userEmail);
		applyBtn.click();
	}

	public void selectEamilAndGetINviteLink(String emailid) {
		driver.findElement(By.xpath(email.replace("?", emailid.toLowerCase()))).click();
		String invite = driver.findElement(By.xpath("//div/label[contains(text(),'Body')]/following-sibling::pre")).getText();
		String[] parts = invite.split("\\s+");
		
		for (String item : parts) {
			if (item.contains("https://")) {
				DataStore.saveValue(UsableValues.INVITE_LINK, item);
				break;
			}
		}
	}

	public void getInviteLink(String emailid) {
		driver.findElement(By.xpath(email.replace("?", emailid))).click();
	}
}
