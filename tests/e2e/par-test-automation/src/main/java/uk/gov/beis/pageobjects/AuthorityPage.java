package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class AuthorityPage extends BasePageObject {

	public AuthorityPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	private String authority = "//label[contains(text(),'?')]";

	public PartnershipTypePage selectAuthority(String auth) {
		WebElement link = driver.findElement(By.xpath(authority.replace("?", auth)));
		link.click();
		continueBtn.click();
		return PageFactory.initElements(driver, PartnershipTypePage.class);
	}

}
