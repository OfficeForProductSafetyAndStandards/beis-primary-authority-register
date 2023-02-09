package uk.gov.beis.pageobjects;

import java.io.IOException;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.FindBy;
import org.openqa.selenium.support.PageFactory;

public class MemberListPage extends BasePageObject {
	public MemberListPage() throws ClassNotFoundException, IOException {
		super();
	}

	@FindBy(xpath = "//input[contains(@value,'Continue')]")
	WebElement continueBtn;

	String memberSize = "//select/option[contains(text(),'?')]";

	public TradingPage selectMemberSize(String size) {
		driver.findElement(By.xpath(memberSize.replace("?", size))).click();
		continueBtn.click();
		return PageFactory.initElements(driver, TradingPage.class);
	}
}
