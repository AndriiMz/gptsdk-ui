import moment from "moment";

export const momentToElapsed = (date, maxHours = 3) => {
    const minutes = moment().diff(date, 'minutes')
    if (minutes < 1) {
        return 'Just now'
    }

    if (minutes < 60) {
        return `${minutes} minutes ago`
    }

    if (minutes < maxHours * 60) {
        return `${Math.floor(minutes / 60)} hours ago`
    }

    return moment(date).format('HH:mm A, DD MMM')
}
