package uk.gov.beis.utility;

import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.Date;

import uk.gov.beis.helper.PropertiesUtil;

public class DateFormatter {

	/**
	 * For use in Backend framework calls to this method, i.e. classes, methods, in
	 * order to retrieve the current date in the provided SimpleDateFormat pattern.
	 * 
	 * @param format - The SimpleDateFormat pattern to format the current date in.
	 * @return Current date formatted.
	 */
	public static String getDynamicDate(String format) {
		return getDateInPastFuture(new Date(), format, PropertiesUtil.getConfigPropertyValue("future_past_days"));
	}

	/**
	 * Adds/Subtracts the number of Calendar days specified to the current date at
	 * runtime, and returns this date in the specified format provided.
	 * 
	 * @param date         - The Date object we wish to format.
	 * @param format       - The SimpleDateFormat pattern to format the date in.
	 * @param CalendarDays - Number of calendar days to add/subtract from the
	 *                     current date.
	 * @return Formatted Date object.
	 */
	public static String getDateInPastFuture(Date date, String format, String calendarDays) {
		Integer days = calendarDays == null || calendarDays.isEmpty() ? 0 : Integer.parseInt(calendarDays);
		Calendar cal = Calendar.getInstance();
		cal.setTime(date);
		cal.add(Calendar.DAY_OF_MONTH, days);
		return formatDate(cal.getTime(), format);
	}

	private static String formatDate(Date date, String format) {
		return new SimpleDateFormat(format).format(date);
	}

}
