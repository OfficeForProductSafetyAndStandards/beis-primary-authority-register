package uk.gov.beis.pageobjects.OtherPageObjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

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
	
	private String email = "//tr/td[contains(text(),'?')]/preceding-sibling::td/a[contains(text(),'Invitation')]";
	
	public MailLogPage() throws ClassNotFoundException, IOException {
		super();
	}
	
	public MailLogPage navigateToUrl() throws InterruptedException {
		ScenarioContext.lastDriver.get(PropertiesUtil.getConfigPropertyValue("maillog_url"));
		return PageFactory.initElements(driver, MailLogPage.class);
	}
	
	public void searchForUserAccountInvite(String userEmail) {
		toSearchBox.sendKeys(userEmail);
		applyBtn.click();
	}

	public MailLogPage selectEamilAndGetINviteLink(String emailid) {
		driver.findElement(By.xpath(email.replace("?", emailid))).click();
		String invite = driver.findElement(By.xpath("//div/label[contains(text(),'Body')]/following-sibling::pre")).getText();
		String[] parts = invite.split("\\s+");
		
		for (String item : parts) {
			if (item.contains("https://")) {
				DataStore.saveValue(UsableValues.INVITE_LINK, item);
				break;
			}
		}
		return PageFactory.initElements(driver, MailLogPage.class);
	}

	public MailLogPage getInviteLink(String emailid) {
		driver.findElement(By.xpath(email.replace("?", emailid))).click();
		return PageFactory.initElements(driver, MailLogPage.class);
	}
}
