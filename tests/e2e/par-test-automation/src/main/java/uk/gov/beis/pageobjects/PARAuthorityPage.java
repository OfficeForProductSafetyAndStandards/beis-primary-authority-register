package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class PARAuthorityPage extends BasePageObject {

	public PARAuthorityPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String authority = "//label[contains(text(),'?')]";

	public PARPartnershipTypePage selectAuthority(String auth) {
		WebElement link = driver.findElement(By.xpath(authority.replace("?", auth)));
		link.click();
		if (continueBtn.isDisplayed())
			continueBtn.click();
		return PageFactory.initElements(driver, PARPartnershipTypePage.class);
	}

}